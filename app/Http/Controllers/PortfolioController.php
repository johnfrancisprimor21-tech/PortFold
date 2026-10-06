<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    // -------------------------------------------------------
    // Supabase Storage helper
    // -------------------------------------------------------
    private function uploadToSupabase(UploadedFile $file, string $bucket, string $folder): ?string
    {
        $projectUrl = rtrim((string) config('services.supabase.url'), '/');
        $serviceKey = (string) config('services.supabase.service_key');
        if ($projectUrl === '' || $serviceKey === '') {
            return null;
        }

        $ext = $file->extension();
        $filename = $folder.'/'.Str::uuid().'.'.$ext;

        $response = Http::connectTimeout(5)->timeout(20)->withHeaders([
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => $file->getMimeType(),
        ])->withBody(
            file_get_contents($file->getRealPath()),
            $file->getMimeType()
        )->post("{$projectUrl}/storage/v1/object/{$bucket}/{$filename}");

        if ($response->successful()) {
            return "{$projectUrl}/storage/v1/object/public/{$bucket}/{$filename}";
        }

        return null;
    }

    /** Remove only objects in this app's known public Supabase buckets. */
    private function deleteSupabaseObjects(array $urls): void
    {
        $projectUrl = rtrim((string) config('services.supabase.url'), '/');
        $serviceKey = (string) config('services.supabase.service_key');
        $projectHost = parse_url($projectUrl, PHP_URL_HOST);
        if ($projectUrl === '' || $serviceKey === '' || ! is_string($projectHost)) {
            return;
        }

        $objects = ['avatars' => [], 'projects' => []];
        foreach ($urls as $url) {
            if (! is_string($url) || ! $this->isSafeHttpUrl($url)) {
                continue;
            }

            $parts = parse_url($url);
            if (strtolower((string) ($parts['host'] ?? '')) !== strtolower($projectHost)
                || ($parts['port'] ?? null) !== parse_url($projectUrl, PHP_URL_PORT)
                || strtolower((string) ($parts['scheme'] ?? '')) !== strtolower((string) parse_url($projectUrl, PHP_URL_SCHEME))) {
                continue;
            }

            $path = rawurldecode((string) ($parts['path'] ?? ''));
            if (! preg_match('~^/storage/v1/object/public/(avatars|projects)/([A-Za-z0-9._/-]+)$~', $path, $matches)) {
                continue;
            }

            $bucket = $matches[1];
            $objectPath = $matches[2];
            $expectedFolder = $bucket === 'avatars' ? 'profiles/' : 'screenshots/';
            if (! str_starts_with($objectPath, $expectedFolder)
                || in_array('..', explode('/', $objectPath), true)) {
                continue;
            }

            $objects[$bucket][] = $objectPath;
        }

        foreach ($objects as $bucket => $paths) {
            $paths = array_values(array_unique($paths));
            if ($paths === []) {
                continue;
            }

            try {
                $response = Http::connectTimeout(3)->timeout(10)->withHeaders([
                    'Authorization' => 'Bearer '.$serviceKey,
                    'apikey' => $serviceKey,
                    'Content-Type' => 'application/json',
                ])->send('DELETE', "{$projectUrl}/storage/v1/object/{$bucket}", [
                    'json' => ['prefixes' => $paths],
                ]);

                if (! $response->successful()) {
                    Log::warning('Supabase portfolio asset cleanup failed.', [
                        'bucket' => $bucket,
                        'object_count' => count($paths),
                        'status' => $response->status(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::warning('Supabase portfolio asset cleanup request failed.', [
                    'bucket' => $bucket,
                    'object_count' => count($paths),
                    'exception' => $exception::class,
                ]);
            }
        }
    }

    // Same rules for create and edit so both behave the same
    private function infoRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'headline' => 'nullable|string|max:120',
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'phone_country_code' => 'nullable|string|regex:/^\+[1-9][0-9]{0,2}$/',
            'phone_number' => 'nullable|string|max:35',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,webp|dimensions:max_width=4096,max_height=4096|max:4096',
            'skills' => 'nullable|string|max:1200',
            'projects' => 'nullable|array|max:8',
            'projects.*.title' => 'nullable|string|max:160',
            'projects.*.description' => 'nullable|string|max:2000',
            'projects.*.live_url' => ['nullable', 'string', 'url', 'max:2048', 'regex:~\\Ahttps?://~i'],
            'projects.*.repo_url' => ['nullable', 'string', 'url', 'max:2048', 'regex:~\\Ahttps?://~i'],
            'projects.*.screenshot' => 'nullable|image|mimes:jpeg,png,webp|dimensions:max_width=4096,max_height=4096|max:4096',
            'education' => 'nullable|array|max:10',
            'education.*.institution' => 'nullable|string|max:255',
            'education.*.degree' => 'nullable|string|max:255',
            'education.*.field' => 'nullable|string|max:255',
            'education.*.start_year' => 'nullable|string|max:12',
            'education.*.end_year' => 'nullable|string|max:12',
            'experiences' => 'nullable|array|max:10',
            'experiences.*.company' => 'nullable|string|max:255',
            'experiences.*.role' => 'nullable|string|max:255',
            'experiences.*.start_date' => 'nullable|string|max:80',
            'experiences.*.end_date' => 'nullable|string|max:80',
            'experiences.*.description' => 'nullable|string|max:2000',
            'experiences.*.is_internship' => 'nullable|boolean',
            'links' => 'nullable|array:github,linkedin,website,facebook',
            'links.*' => ['nullable', 'string', 'url', 'max:2048', 'regex:~\\Ahttps?://~i'],
        ];
    }

    private function ownedPortfolio(string $id): object
    {
        return DB::table('portfolios')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function isSafeHttpUrl(?string $url): bool
    {
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            && parse_url($url, PHP_URL_HOST) !== null;
    }

    /** @return array{0: Collection, 1: Collection} */
    private function sanitizePortfolioUrls(Collection $projects, Collection $links, ?object $info = null): array
    {
        if ($info !== null && ! $this->isSafeHttpUrl($info->photo_url ?? null)) {
            $info->photo_url = null;
        }

        $projects = $projects->map(function ($project) {
            foreach (['live_url', 'repo_url', 'screenshot_url'] as $field) {
                if (! $this->isSafeHttpUrl($project->{$field} ?? null)) {
                    $project->{$field} = null;
                }
            }

            return $project;
        });

        $allowedPlatforms = ['github', 'linkedin', 'website', 'facebook'];
        $links = $links->filter(fn ($link) => in_array(strtolower((string) ($link->platform ?? '')), $allowedPlatforms, true)
            && $this->isSafeHttpUrl($link->url ?? null))->values();

        return [$projects, $links];
    }

    private function preparePhoneInput(Request $request): void
    {
        if (! $request->exists('phone_number') && ! $request->exists('phone_country_code')) {
            return;
        }

        $numberInput = $request->input('phone_number', '');
        $number = is_scalar($numberInput) ? trim((string) $numberInput) : '';
        $countryCodeInput = $request->input('phone_country_code', '+63');
        $countryCode = is_string($countryCodeInput) ? trim($countryCodeInput) : '+63';
        if (Str::startsWith($number, $countryCode)) {
            $number = trim(substr($number, strlen($countryCode)));
        }
        if ($countryCode === '+63') {
            $number = preg_replace('/^0/', '', $number);
        }
        if ($number === '') {
            $request->merge(['phone' => null]);

            return;
        }

        $request->merge(['phone' => trim($countryCode.' '.$number)]);
    }

    private function infoNames(): array
    {
        return [
            'full_name' => 'full name',
            'contact_email' => 'email',
            'phone_country_code' => 'country calling code',
            'phone_number' => 'contact number',
            'profile_photo' => 'profile photo',
            'bio' => 'about me',
        ];
    }

    // -------------------------------------------------------
    // Show the portfolio info form
    // -------------------------------------------------------
    public function create(): View
    {
        return view('portfolio.create');
    }

    // -------------------------------------------------------
    // Save portfolio + all related info
    // -------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $this->preparePhoneInput($request);
        $request->validate($this->infoRules(), [], $this->infoNames());

        $uploadFailed = false;

        // Upload profile photo if provided
        $photoUrl = null;
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $photoUrl = $this->uploadToSupabase($request->file('profile_photo'), 'avatars', 'profiles');
            if (! $photoUrl) {
                $uploadFailed = true;
            }
        }

        // 1. Create portfolio row
        $portfolioId = (string) Str::uuid();
        $slug = Str::slug($request->full_name).'-'.Str::random(5);

        DB::table('portfolios')->insert([
            'id' => $portfolioId,
            'user_id' => Auth::id(),
            'template_id' => DB::table('templates')->where('slug', 'simple')->value('id'),
            'slug' => $slug,
            'status' => 'draft',
            'updated_at' => now(),
        ]);

        // 2. Save portfolio_info (now with full_name + photo_url)
        DB::table('portfolio_info')->insert([
            'id' => (string) Str::uuid(),
            'portfolio_id' => $portfolioId,
            'full_name' => $request->full_name,
            'headline' => $request->headline,
            'bio' => $request->bio,
            'location' => $request->location,
            'contact_email' => $request->contact_email,
            'phone' => $request->phone,
            'photo_url' => $photoUrl,
        ]);

        // 3. Save skills
        if ($request->filled('skills')) {
            $skills = array_filter(array_map('trim', explode(',', $request->skills)));
            foreach ($skills as $skill) {
                DB::table('skills')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $portfolioId,
                    'name' => $skill,
                ]);
            }
        }

        // 4. Save projects (with optional screenshot upload)
        if ($request->has('projects')) {
            $displayOrder = 0;
            foreach ($request->projects as $i => $project) {
                if (empty($project['title'])) {
                    continue;
                }

                $screenshotUrl = null;
                $fileKey = "projects.{$i}.screenshot";
                if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                    $screenshotUrl = $this->uploadToSupabase(
                        $request->file($fileKey), 'projects', 'screenshots'
                    );
                    if (! $screenshotUrl) {
                        $uploadFailed = true;
                    }
                }

                DB::table('projects')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $portfolioId,
                    'title' => $project['title'],
                    'description' => $project['description'] ?? null,
                    'live_url' => $project['live_url'] ?? null,
                    'repo_url' => $project['repo_url'] ?? null,
                    'screenshot_url' => $screenshotUrl,
                    'display_order' => $displayOrder++,
                ]);
            }
        }

        // 5. Save education
        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                if (empty($edu['institution'])) {
                    continue;
                }
                DB::table('education')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $portfolioId,
                    'institution' => $edu['institution'],
                    'degree' => $edu['degree'] ?? null,
                    'field' => $edu['field'] ?? null,
                    'start_year' => $edu['start_year'] ?? null,
                    'end_year' => $edu['end_year'] ?? null,
                ]);
            }
        }

        // 6. Save experiences
        if ($request->has('experiences')) {
            foreach ($request->experiences as $exp) {
                if (empty($exp['company'])) {
                    continue;
                }
                DB::table('experiences')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $portfolioId,
                    'company' => $exp['company'],
                    'role' => $exp['role'] ?? null,
                    'start_date' => $exp['start_date'] ?? null,
                    'end_date' => $exp['end_date'] ?? null,
                    'description' => $exp['description'] ?? null,
                    'is_internship' => isset($exp['is_internship']) ? true : false,
                ]);
            }
        }

        // 7. Save links
        if ($request->has('links')) {
            foreach ($request->links as $platform => $url) {
                if (empty($url)) {
                    continue;
                }
                DB::table('links')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $portfolioId,
                    'platform' => $platform,
                    'url' => $url,
                ]);
            }
        }

        return redirect()->route('portfolio.template', $portfolioId)
            ->with('success', 'Your info is saved. Now pick a template.')
            ->with('error', $uploadFailed ? 'Your info was saved, but an image could not be uploaded. You can add it again from Edit info.' : null);
    }

    // -------------------------------------------------------
    // Show template selection page
    // -------------------------------------------------------
    public function selectTemplate($id): View
    {
        $portfolio = $this->ownedPortfolio((string) $id);
        $templates = DB::table('templates')->get();

        return view('portfolio.template', compact('portfolio', 'templates'));
    }

    // -------------------------------------------------------
    // Apply chosen template
    // -------------------------------------------------------
    public function applyTemplate(Request $request, $id): RedirectResponse
    {
        $this->ownedPortfolio((string) $id);
        $request->validate(['template_id' => 'required|uuid|exists:templates,id']);

        DB::table('portfolios')->where('id', $id)->update([
            'template_id' => $request->template_id,
            'status' => 'published',
            'published_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('portfolio.preview', $id)
            ->with('success', 'Your portfolio is ready. This is how it looks.');
    }

    // -------------------------------------------------------
    // Preview the generated portfolio
    // -------------------------------------------------------
    public function preview($id): Response
    {
        $portfolio = DB::table('portfolios')->where('id', $id)->firstOrFail();
        $template = DB::table('templates')->where('id', $portfolio->template_id)->first();
        $info = DB::table('portfolio_info')->where('portfolio_id', $id)->first();
        $skills = DB::table('skills')->where('portfolio_id', $id)->get();
        $projects = DB::table('projects')->where('portfolio_id', $id)->orderBy('display_order')->get();
        $education = DB::table('education')->where('portfolio_id', $id)->get();
        $experiences = DB::table('experiences')->where('portfolio_id', $id)->get();
        $links = DB::table('links')->where('portfolio_id', $id)->get();
        [$projects, $links] = $this->sanitizePortfolioUrls($projects, $links, $info);

        $view = 'portfolio.templates.'.$template->slug;

        $html = view($view, compact(
            'portfolio', 'template', 'info', 'skills', 'projects', 'education', 'experiences', 'links'
        ))->render();

        // Add the floating controls (back / edit / change template) without touching each template file
        $bar = view('portfolio.partials.preview-bar', compact('portfolio'))->render();
        $pos = strripos($html, '</body>');
        $html = $pos !== false ? substr($html, 0, $pos).$bar.substr($html, $pos) : $html.$bar;

        return response($html);
    }

    public function show(string $id): Response
    {
        return $this->preview($id);
    }

    /** Download the user-facing portfolio content as a portable JSON file. */
    public function export(string $id): Response
    {
        $portfolio = $this->ownedPortfolio($id);
        $template = DB::table('templates')->where('id', $portfolio->template_id)->first(['name', 'slug']);
        $info = DB::table('portfolio_info')
            ->where('portfolio_id', $id)
            ->first(['full_name', 'headline', 'bio', 'location', 'contact_email', 'phone', 'photo_url']);

        $projects = DB::table('projects')
            ->where('portfolio_id', $id)
            ->orderBy('display_order')
            ->get(['title', 'description', 'live_url', 'repo_url', 'screenshot_url', 'display_order']);
        $links = DB::table('links')->where('portfolio_id', $id)->get(['platform', 'url']);
        [$projects, $links] = $this->sanitizePortfolioUrls($projects, $links, $info);

        $export = [
            'format' => 'portfold-portfolio',
            'schema_version' => 1,
            'exported_at' => now()->toIso8601String(),
            'portfolio' => [
                'template' => $template,
                'profile' => $info,
                'skills' => DB::table('skills')
                    ->where('portfolio_id', $id)
                    ->get(['name'])
                    ->all(),
                'projects' => $projects->all(),
                'experience' => DB::table('experiences')
                    ->where('portfolio_id', $id)
                    ->get(['role', 'company', 'start_date', 'end_date', 'description', 'is_internship'])
                    ->all(),
                'education' => DB::table('education')
                    ->where('portfolio_id', $id)
                    ->get(['institution', 'degree', 'field', 'start_year', 'end_year'])
                    ->all(),
                'links' => $links->all(),
            ],
        ];

        $filenameSource = $info->full_name ?? $portfolio->slug ?? 'portfolio';
        $filename = (Str::slug($filenameSource) ?: 'portfolio').'-portfolio.json';
        $json = json_encode(
            $export,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        return response($json, 200, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Download a static HTML snapshot of the portfolio. */
    public function exportHtml(string $id): Response
    {
        $portfolio = $this->ownedPortfolio($id);
        $template = DB::table('templates')->where('id', $portfolio->template_id)->first();
        $info = DB::table('portfolio_info')->where('portfolio_id', $id)->first();
        $skills = DB::table('skills')->where('portfolio_id', $id)->get();
        $projects = DB::table('projects')->where('portfolio_id', $id)->orderBy('display_order')->get();
        $education = DB::table('education')->where('portfolio_id', $id)->get();
        $experiences = DB::table('experiences')->where('portfolio_id', $id)->get();
        $links = DB::table('links')->where('portfolio_id', $id)->get();
        [$projects, $links] = $this->sanitizePortfolioUrls($projects, $links, $info);

        $view = 'portfolio.templates.'.$template->slug;
        $html = view($view, compact(
            'portfolio', 'template', 'info', 'skills', 'projects', 'education', 'experiences', 'links'
        ))->render();

        if ($template->slug === 'creative' && str_contains($html, 'data-creative-scene')) {
            $html = $this->inlineCreativeHeroScript($html);
        }

        $filenameSource = $info->full_name ?? $portfolio->slug ?? 'portfolio';
        $filename = (Str::slug($filenameSource) ?: 'portfolio').'-portfolio.html';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function inlineCreativeHeroScript(string $html): string
    {
        $manifestPath = public_path('build/manifest.json');
        if (! is_readable($manifestPath)) {
            return $html;
        }

        try {
            $manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $html;
        }

        $assetPath = $manifest['resources/js/creative-hero.js']['file'] ?? null;
        if (! is_string($assetPath) || ! str_starts_with($assetPath, 'assets/')) {
            return $html;
        }

        $bundlePath = public_path('build/'.$assetPath);
        if (! is_readable($bundlePath)) {
            return $html;
        }

        $bundle = file_get_contents($bundlePath);
        if ($bundle === false) {
            return $html;
        }

        $html = preg_replace(
            '~<link\b(?=[^>]*\brel=["\']modulepreload["\'])(?=[^>]*\bhref=["\'][^"\']*creative-hero[^"\']*["\'])[^>]*>\s*~i',
            '',
            $html
        );
        $html = preg_replace(
            '~<script\b(?=[^>]*\bsrc=["\'][^"\']*(?:@vite/client|creative-hero)[^"\']*["\'])[^>]*>\s*</script>~i',
            '',
            $html
        );

        $inlineScript = '<script>'.str_ireplace('</script', '<\/script', $bundle).'</script>';
        $bodyEnd = strripos($html, '</body>');

        if ($bodyEnd === false) {
            return $html.$inlineScript;
        }

        return substr($html, 0, $bodyEnd).$inlineScript.substr($html, $bodyEnd);
    }

    // -------------------------------------------------------
    // Show the edit form
    // -------------------------------------------------------
    public function edit($id): View
    {
        $portfolio = $this->ownedPortfolio((string) $id);
        $info = DB::table('portfolio_info')->where('portfolio_id', $id)->first();
        $skills = DB::table('skills')->where('portfolio_id', $id)->get();
        $projects = DB::table('projects')->where('portfolio_id', $id)->orderBy('display_order')->get();
        $education = DB::table('education')->where('portfolio_id', $id)->get();
        $experiences = DB::table('experiences')->where('portfolio_id', $id)->get();
        $links = DB::table('links')->where('portfolio_id', $id)->get();

        return view('portfolio.edit', compact(
            'portfolio', 'info', 'skills', 'projects', 'education', 'experiences', 'links'
        ));
    }

    // -------------------------------------------------------
    // Update portfolio
    // -------------------------------------------------------
    public function update(Request $request, $id): RedirectResponse
    {
        $this->ownedPortfolio((string) $id);
        $this->preparePhoneInput($request);
        $request->validate($this->infoRules(), [], $this->infoNames());

        $current = DB::table('portfolio_info')->where('portfolio_id', $id)->first();
        $uploadFailed = false;

        // Upload new photo if provided, otherwise (or if the upload fails) keep the existing one
        $photoUrl = $current->photo_url ?? null;
        $replacedPhotoUrl = null;
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $newUrl = $this->uploadToSupabase($request->file('profile_photo'), 'avatars', 'profiles');
            if ($newUrl) {
                $replacedPhotoUrl = $photoUrl;
                $photoUrl = $newUrl;
            } else {
                $uploadFailed = true;
            }
        }

        DB::table('portfolio_info')->where('portfolio_id', $id)->update([
            'full_name' => $request->full_name,
            'headline' => $request->headline,
            'bio' => $request->bio,
            'location' => $request->location,
            'contact_email' => $request->contact_email,
            'phone' => $request->phone,
            'photo_url' => $photoUrl,
        ]);

        // Re-save skills
        DB::table('skills')->where('portfolio_id', $id)->delete();
        if ($request->filled('skills')) {
            $skills = array_filter(array_map('trim', explode(',', $request->skills)));
            foreach ($skills as $skill) {
                DB::table('skills')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $id,
                    'name' => $skill,
                ]);
            }
        }

        // Re-save projects (keep existing screenshot if no new upload)
        $oldProjects = DB::table('projects')->where('portfolio_id', $id)
            ->orderBy('display_order')->get()->keyBy('display_order');
        $oldScreenshotUrls = $oldProjects->pluck('screenshot_url')->filter()->all();
        $newScreenshotUrls = [];
        DB::table('projects')->where('portfolio_id', $id)->delete();

        if ($request->has('projects')) {
            $displayOrder = 0;
            foreach ($request->projects as $i => $project) {
                if (empty($project['title'])) {
                    continue;
                }

                $screenshotUrl = $oldProjects->get($i)->screenshot_url ?? null;
                $fileKey = "projects.{$i}.screenshot";
                if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                    $newShot = $this->uploadToSupabase(
                        $request->file($fileKey), 'projects', 'screenshots'
                    );
                    if ($newShot) {
                        $screenshotUrl = $newShot;
                    } else {
                        $uploadFailed = true;
                    }
                }

                DB::table('projects')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $id,
                    'title' => $project['title'],
                    'description' => $project['description'] ?? null,
                    'live_url' => $project['live_url'] ?? null,
                    'repo_url' => $project['repo_url'] ?? null,
                    'screenshot_url' => $screenshotUrl,
                    'display_order' => $displayOrder++,
                ]);
                if ($screenshotUrl !== null) {
                    $newScreenshotUrls[] = $screenshotUrl;
                }
            }
        }

        // Re-save education
        DB::table('education')->where('portfolio_id', $id)->delete();
        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                if (empty($edu['institution'])) {
                    continue;
                }
                DB::table('education')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $id,
                    'institution' => $edu['institution'],
                    'degree' => $edu['degree'] ?? null,
                    'field' => $edu['field'] ?? null,
                    'start_year' => $edu['start_year'] ?? null,
                    'end_year' => $edu['end_year'] ?? null,
                ]);
            }
        }

        // Re-save experiences
        DB::table('experiences')->where('portfolio_id', $id)->delete();
        if ($request->has('experiences')) {
            foreach ($request->experiences as $exp) {
                if (empty($exp['company'])) {
                    continue;
                }
                DB::table('experiences')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $id,
                    'company' => $exp['company'],
                    'role' => $exp['role'] ?? null,
                    'start_date' => $exp['start_date'] ?? null,
                    'end_date' => $exp['end_date'] ?? null,
                    'description' => $exp['description'] ?? null,
                    'is_internship' => isset($exp['is_internship']) ? true : false,
                ]);
            }
        }

        // Re-save links
        DB::table('links')->where('portfolio_id', $id)->delete();
        if ($request->has('links')) {
            foreach ($request->links as $platform => $url) {
                if (empty($url)) {
                    continue;
                }
                DB::table('links')->insert([
                    'id' => (string) Str::uuid(),
                    'portfolio_id' => $id,
                    'platform' => $platform,
                    'url' => $url,
                ]);
            }
        }

        DB::table('portfolios')->where('id', $id)->update(['updated_at' => now()]);

        $this->deleteSupabaseObjects(array_merge(
            $replacedPhotoUrl !== null && $replacedPhotoUrl !== $photoUrl ? [$replacedPhotoUrl] : [],
            array_values(array_diff($oldScreenshotUrls, $newScreenshotUrls))
        ));

        return redirect()->route('portfolio.preview', $id)
            ->with('success', 'Changes saved.')
            ->with('error', $uploadFailed ? 'Your changes were saved, but an image could not be uploaded. Your previous image was kept.' : null);
    }

    // -------------------------------------------------------
    // Delete portfolio
    // -------------------------------------------------------
    public function destroy($id): RedirectResponse
    {
        $this->ownedPortfolio((string) $id);
        $storedUrls = array_filter(array_merge(
            [DB::table('portfolio_info')->where('portfolio_id', $id)->value('photo_url')],
            DB::table('projects')->where('portfolio_id', $id)->whereNotNull('screenshot_url')->pluck('screenshot_url')->all()
        ));

        DB::transaction(function () use ($id): void {
            foreach (['portfolio_info', 'skills', 'projects', 'education', 'experiences', 'links'] as $table) {
                DB::table($table)->where('portfolio_id', $id)->delete();
            }

            DB::table('portfolios')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->delete();
        });

        $this->deleteSupabaseObjects($storedUrls);

        return redirect()->route('portfolio.manage')
            ->with('success', 'Portfolio deleted.');
    }

    // -------------------------------------------------------
    // Manage page
    // -------------------------------------------------------
    public function manage(): View
    {
        $portfolios = DB::table('portfolios')
            ->where('portfolios.user_id', Auth::id())
            ->join('portfolio_info', 'portfolios.id', '=', 'portfolio_info.portfolio_id')
            ->join('templates', 'portfolios.template_id', '=', 'templates.id')
            ->select(
                'portfolios.id',
                'portfolios.slug',
                'portfolios.status',
                'portfolios.updated_at',
                'portfolio_info.full_name',
                'portfolio_info.headline',
                'templates.name as template_name',
                'templates.slug as template_slug'
            )
            ->orderByDesc('portfolios.updated_at')
            ->paginate(25);

        return view('portfolio.manage', compact('portfolios'));
    }
}
