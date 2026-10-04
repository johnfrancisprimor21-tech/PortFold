<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PortfolioEditViewTest extends TestCase
{
    public function test_edit_page_posts_save_button_as_multipart_put_request(): void
    {
        [$user, $portfolioId] = $this->createOwnedPortfolio();

        $response = $this->actingAs($user)->get(route('portfolio.edit', $portfolioId));
        $html = (string) $response->getContent();
        $formStart = strpos($html, '<form id="portfolio-edit-form"');
        $formEnd = $formStart === false ? false : strpos($html, '</form>', $formStart);
        $saveButton = $formStart === false ? false : strpos($html, 'Save changes', $formStart);
        $form = $formStart === false || $formEnd === false ? '' : substr($html, $formStart, $formEnd - $formStart);

        $response->assertOk();
        $this->assertNotFalse($formStart);
        $this->assertNotFalse($formEnd);
        $this->assertNotFalse($saveButton);
        $this->assertLessThan($formEnd, $saveButton);
        $this->assertStringContainsString('action="'.route('portfolio.update', $portfolioId).'"', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('enctype="multipart/form-data"', $form);
        $this->assertStringContainsString('name="_method" value="PUT"', $form);
        $this->assertStringContainsString('name="_token"', $form);
    }

    public function test_invalid_save_keeps_added_project_text_in_the_edit_form(): void
    {
        [$user, $portfolioId] = $this->createOwnedPortfolio();
        $editUrl = route('portfolio.edit', $portfolioId);

        $this->actingAs($user)
            ->from($editUrl)
            ->put(route('portfolio.update', $portfolioId), [
                'full_name' => 'Test Portfolio Owner',
                'contact_email' => 'not-an-email',
                'projects' => [
                    1 => [
                        'title' => 'New project draft',
                        'description' => 'This draft should remain after validation.',
                        'live_url' => 'https://example.test/project',
                    ],
                ],
            ])
            ->assertRedirect($editUrl)
            ->assertSessionHasErrors('contact_email');

        $this->get($editUrl)
            ->assertOk()
            ->assertSee('value="New project draft"', false)
            ->assertSee('This draft should remain after validation.')
            ->assertSee('value="https://example.test/project"', false);

        $this->assertDatabaseMissing('projects', ['title' => 'New project draft']);
    }

    public function test_valid_edit_saves_a_new_project(): void
    {
        [$user, $portfolioId] = $this->createOwnedPortfolio();

        $this->actingAs($user)
            ->put(route('portfolio.update', $portfolioId), [
                'full_name' => 'Test Portfolio Owner',
                'contact_email' => 'owner@example.test',
                'projects' => [
                    1 => [
                        'title' => 'A newly added project',
                        'description' => 'A project submitted from the edit form.',
                        'live_url' => 'https://example.test/project',
                        'repo_url' => 'https://github.com/example/project',
                    ],
                ],
            ])
            ->assertRedirect(route('portfolio.preview', $portfolioId));

        $this->assertDatabaseHas('projects', [
            'portfolio_id' => $portfolioId,
            'title' => 'A newly added project',
            'description' => 'A project submitted from the edit form.',
            'display_order' => 0,
        ]);
        $this->assertDatabaseHas('portfolio_info', [
            'portfolio_id' => $portfolioId,
            'full_name' => 'Test Portfolio Owner',
            'contact_email' => 'owner@example.test',
        ]);
    }

    /** @return array{User, string} */
    private function createOwnedPortfolio(): array
    {
        $this->createPortfolioSchema();
        $user = User::factory()->create();
        $portfolioId = 'c29504c3-2fe0-4d6a-8f2f-fde13ee8ff98';

        DB::table('portfolios')->insert([
            'id' => $portfolioId,
            'user_id' => $user->id,
            'slug' => 'test-portfolio',
            'status' => 'draft',
        ]);
        DB::table('portfolio_info')->insert([
            'id' => 'a6d57fc0-c2e0-4dd6-80f7-81b125b9d81c',
            'portfolio_id' => $portfolioId,
            'full_name' => 'Test Portfolio Owner',
            'contact_email' => 'owner@example.test',
        ]);

        return [$user, $portfolioId];
    }

    private function createPortfolioSchema(): void
    {
        foreach (['links', 'experiences', 'education', 'projects', 'skills', 'portfolio_info', 'portfolios', 'users'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->text('avatar_url')->nullable();
            $table->timestampTz('created_at')->nullable();
        });
        Schema::create('portfolios', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('template_id')->nullable();
            $table->string('slug');
            $table->string('status');
            $table->timestampTz('updated_at')->nullable();
        });
        Schema::create('portfolio_info', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('full_name');
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_email');
            $table->string('phone')->nullable();
            $table->text('photo_url')->nullable();
        });
        Schema::create('skills', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('name');
        });
        Schema::create('projects', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('live_url')->nullable();
            $table->text('repo_url')->nullable();
            $table->text('screenshot_url')->nullable();
            $table->unsignedInteger('display_order');
        });
        Schema::create('education', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('institution');
            $table->string('degree')->nullable();
            $table->string('field')->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
        });
        Schema::create('experiences', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('company');
            $table->string('role')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_internship')->default(false);
        });
        Schema::create('links', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('portfolio_id');
            $table->string('platform');
            $table->text('url');
        });
    }
}
