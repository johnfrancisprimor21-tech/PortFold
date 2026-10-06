# PortFold Project Documentation

## Project information

| Item | Details |
| --- | --- |
| Project title | PortFold — Online Portfolio Template Generator |
| Student / team | Primor John Francis C. |
| Description | A web application for creating, saving, previewing, managing, and exporting personal portfolios using three selectable templates, with recoverable Trash and optional permanent deletion. |
| Published website | [https://portfold.onrender.com](https://portfold.onrender.com) |
| Source code | [https://github.com/johnfrancisprimor21-tech/PortFold](https://github.com/johnfrancisprimor21-tech/PortFold) |
| Hosting | Render web service, Docker runtime |
| Database | Supabase PostgreSQL |
| Authentication and image storage | Supabase Auth and Supabase Storage |

Do not add passwords, API secrets, database connection strings, or service-role keys to this document or the submitted source code.

## Project description

PortFold helps people publish a portfolio without hand-coding a personal website. A signed-in user enters profile and career information, selects a visual template, previews the result, and later manages or exports the saved portfolio. Portfolio creation and management require an authenticated account, while generated portfolio pages are public and read-only. A public URL is a share link, not access control: a visitor who has the URL can read its page without signing in. Sensitive content should not be placed in a portfolio.

## Requirements mapping

| Course requirement | PortFold implementation |
| --- | --- |
| Collect portfolio information | Create and edit forms collect name, photo, email, phone, location, biography, education, skills, projects, experience, and links. |
| Provide exactly three noticeably different templates | Simple, Modern, and Creative Blade views are available. `DatabaseSeeder` adds those template rows when the `templates` table is present. |
| Save and retrieve information online | Laravel uses the configured Supabase PostgreSQL connection; uploaded images use Supabase Storage. |
| Select a template and preview the generated portfolio | Template selection saves the chosen template; the preview/public routes render that Blade template. |
| Edit and delete portfolio information | Authenticated owner routes provide edit/update, move-to-Trash, restore, and permanent-delete actions from the Manage page. |
| Publish the system with a working URL | The Render service and public URL are configured. Application code from commit `393a6a9` passed the latest verified Render deployment after the `/up` health-check path was aligned. See the deployment status below and QA checklist. |
| Provide readable, organized, responsive UI | The layouts use mobile breakpoints, visible focus styles, semantic labels, field feedback, and reduced-motion handling. Verify the manual checks in `QA-CHECKLIST.md` before claiming complete accessibility or device coverage. |
| Submit source code, project documentation, and screenshots | The repository includes this report, QA checklist, prototype/issue log, and desktop captures of Simple (light and dark), Modern, Creative, and the email-confirmation screen. The captures use synthetic data. |

## Main user flow

1. Visit the home page.
2. Sign in or create an account.
3. Enter portfolio details and save them.
4. Select Simple, Modern, or Creative.
5. Preview the generated portfolio and use its public page URL.
6. Return to Manage to edit, move the portfolio to Trash, restore it, permanently delete it, or export it.

The create, edit, manage, export, Trash, restore, and permanent-delete actions require the signed-in user to own the portfolio. Public portfolio routes are intended for viewing only. Moving an item to Trash sets `portfolios.deleted_at` and retains child rows and uploaded images; restore clears that timestamp. Permanent deletion is available only for trashed records and removes the associated domain rows and unshared Storage objects. Storage APIs and PostgreSQL do not provide one shared transaction, so an interrupted storage cleanup can leave some images removed while the portfolio remains in Trash for another attempt.

## Pages and routes

| Page / action | Route |
| --- | --- |
| Home | `/` |
| Sign in / account creation | `/login`, `/register` |
| Email confirmation instructions / resend | `/email/confirmation` |
| Create portfolio | `/portfolio/create` |
| Save new portfolio | `POST /portfolio` |
| Choose template | `/portfolio/{id}/template` |
| Apply template | `POST /portfolio/{id}/template` |
| Public preview | `/portfolio/{id}/preview` |
| Public portfolio page | `/portfolio/{id}` |
| Manage portfolios | `/manage` |
| Edit portfolio | `/portfolio/{id}/edit` |
| Save edits / move to Trash | `PUT /portfolio/{id}`, `DELETE /portfolio/{id}` |
| Restore from Trash | `POST /portfolio/{id}/restore` |
| Permanently delete a trashed portfolio | `DELETE /portfolio/{id}/permanent` |
| Download JSON / HTML | `/portfolio/{id}/export`, `/portfolio/{id}/export/html` |

## Portfolio templates

- **Simple:** clean, professional presentation with theme support and a focus on readable profile details.
- **Modern:** card- and section-based portfolio with visual hierarchy and interactive details.
- **Creative:** an interactive gaming-workstation hero scene rendered with Three.js. Visitors can explore the 3D scene, orbit and zoom the camera, switch between front and rear views, open the interactive desktop, and navigate to portfolio sections.

The screenshots below were captured from the app using synthetic demo content. They document the current visual prototypes and the email-confirmation screen, not a usability study or cross-device certification.

| Screenshot | Evidence |
| --- | --- |
| Simple template — light mode | [simple-light.jpg](screenshots/simple-light.jpg) |
| Simple template — dark mode | [simple-dark.jpg](screenshots/simple-dark.jpg) |
| Modern template — neomorphism | [modern.jpg](screenshots/modern.jpg) |
| Creative template — interactive gaming-workstation scene | [creative.jpg](screenshots/creative.jpg) |
| Manage page — recoverable Trash and permanent-delete control | [manage-trash.jpg](screenshots/manage-trash.jpg) |
| Email confirmation landing page | [email-confirmation.jpg](screenshots/email-confirmation.jpg) |

The portfolio captures use synthetic data. The confirmation capture shows the empty state; the page masks the address when reached after registration in the same browser session. Screenshots document selected UI states and do not certify mobile responsiveness or feature behavior; see the QA checklist.

## Technologies used

- PHP 8.3 or newer and Laravel 13.
- Blade templates for server-rendered pages.
- Vite 8 and Tailwind CSS 4 for frontend asset building.
- Three.js for the Creative template's hero visual.
- Supabase Auth, PostgreSQL, and Storage.
- Render Docker web service for hosting.

Versions above reflect the current Composer/npm manifests and deployment files; review those manifests if the stack changes.

## Database structure

The following is the **logical structure used by the application**, based on its queries, forms, and test fixtures. It documents the fields the app reads or writes; it is not a full schema dump and does not claim unverified constraints.

| Table | Purpose | Application fields in use |
| --- | --- | --- |
| `users` | Supabase-authenticated account profile | `id` (UUID), `full_name`, `email`, `avatar_url`, `created_at` |
| `portfolios` | Portfolio ownership, public slug, selected template, and Trash state | `id`, `user_id`, `template_id`, `slug`, `status`, `updated_at`, `deleted_at` |
| `templates` | Available portfolio designs | `id`, `name`, `slug`, `category`, `style_tags` |
| `portfolio_info` | Personal and contact details | `id`, `portfolio_id`, `full_name`, `headline`, `bio`, `location`, `contact_email`, `phone`, `photo_url` |
| `skills` | Portfolio skill names and optional grouping/proficiency metadata | `id`, `portfolio_id`, `name`, `category`, `proficiency` |
| `projects` | Project content, legacy/current image URL references, and ordering | `id`, `portfolio_id`, `title`, `description`, `live_url`, `repo_url`, `image_url`, `screenshot_url`, `display_order` |
| `experiences` | Work history | `id`, `portfolio_id`, `role`, `company`, `start_date`, `end_date`, `description`, `is_internship` |
| `education` | Education history | `id`, `portfolio_id`, `institution`, `degree`, `field`, `start_year`, `end_year` |
| `links` | Social media and website links | `id`, `portfolio_id`, `platform`, `url` |
| `github_stats` | Optional GitHub profile snapshot | `id`, `portfolio_id`, `github_username`, `public_repos`, `followers`, `pinned_repos`, `synced_at` |

Logical relationships: a user owns portfolios; each portfolio selects a template, has its profile information, and may have multiple skills, projects, experiences, education entries, links, and an optional GitHub statistics record. `deleted_at` is a nullable soft-delete timestamp: null means active, non-null means in Trash.

### Supabase schema snapshot — 2026-10-06

The live Supabase schema was inspected through metadata only; no portfolio or account rows were read. The inspected public tables included `cache`, `cache_locks`, `education`, `experiences`, `failed_jobs`, `github_stats`, `job_batches`, `jobs`, `links`, `portfolio_info`, `portfolios`, `projects`, `sessions`, `skills`, `templates`, and `users`. Supabase-managed Auth tables are in the separate `auth` schema.

Metadata confirmed the `deleted_at` column as nullable `timestamp with time zone`. The inspected foreign keys connect `portfolio_info`, `skills`, `projects`, `experiences`, `education`, `links`, and `github_stats` to `portfolios.id` with `ON DELETE CASCADE`; `portfolios.user_id` connects to `users.id` with `ON DELETE CASCADE`; `portfolios.template_id` connects to `templates.id` with `ON DELETE NO ACTION`; and `users.id` connects to `auth.users.id` with `ON DELETE CASCADE`. Inspected uniqueness includes user email, template slug, portfolio slug, one `portfolio_info` row per portfolio, and one `github_stats` row per portfolio. RLS was disabled on the inspected public tables at the time of inspection.

This snapshot is a point-in-time description of the hosted database, not a reproducible schema definition. The `projects.image_url` column was present as well as `screenshot_url`; the current code writes/reads `screenshot_url`, while permanent-delete cleanup checks both columns to cover older rows. `github_stats` is present in the schema and is cleaned up on permanent deletion, but it is not currently a user-facing feature. Reinspect after schema changes and before setting up another environment.

### Database setup limitation to resolve

The repository contains migrations for the users/session tables and portfolio ownership, plus a migration that adds nullable `portfolios.deleted_at` and its index. It does not contain create-table migrations for the portfolio-domain tables listed above. The ownership and soft-delete migrations expect `portfolios` to exist. The seeder adds Simple, Modern, and Creative records only when the `templates` table already exists. The production Supabase schema is already in use, but a fresh database cannot be recreated from the tracked migrations alone.

The checked Supabase metadata snapshot is described above, but a versioned baseline schema or safe setup script is still required for a reproducible database. Never export production records or credentials in that script. The current QA checklist marks this as a release/submission follow-up.

## Hosting and deployment

The current deployment uses Render with a Docker image defined by `Dockerfile` and service configuration in `render.yaml`. The service uses Supabase PostgreSQL over TLS and exposes Laravel's `/up` health endpoint. Required production environment variables are entered privately in Render; do not put their values in GitHub or this report.

Google sign-in also depends on Supabase Auth and Google OAuth configuration. The Google provider must be enabled, the Supabase callback must be registered in Google Cloud, and the PortFold callback URL must be an allowed Supabase redirect. If the OAuth consent screen is in testing mode, the evaluator's account needs to be listed as a test user.

## Current deployment status — 2026-10-05

Render initially timed out the auto-deploy for commit `393a6a9` after 15 minutes 31 seconds. The image build completed; runtime logs showed a successful database connection, no pending migrations, and Apache/PHP-FPM startup. The dashboard did not have an HTTP health-check path, although `render.yaml` specifies `/up`. After I set the dashboard path to `/up`, the retry for `393a6a9` deployed successfully in 46.6 seconds. Render logs show an HTTP 200 from `/up`; read-only external requests to `/`, `/up`, `/login`, `/register`, and `/email/confirmation` returned HTTP 200. Render preserves the previous live version when a new deploy fails its health check ([Render health checks](https://render.com/docs/health-checks)).

The Render free instance may spin down when idle, which can delay the first request by 50 seconds or more. This is a hosting limitation and should be explained during the instructor demo ([Render free services](https://render.com/docs/free)).
## Human-computer interaction choices

- **Visibility of system status:** the creation flow has steps, save feedback, and field-level validation messages.
- **Recognition and recovery during registration:** unconfirmed email registration opens a dedicated next-steps page with a resend action and paths back to sign-in or a different email.
- **User control:** users can preview, return to edit, change templates, and delete their own portfolio.
- **Error prevention and recovery:** required fields, accepted image types, input validation, and preservation of project text after a validation failure help prevent lost work.
- **Consistency and recognition:** navigation, section labels, and template names stay visible across the portfolio flow.
- **Accessibility support:** semantic labels, keyboard focus indicators, accessible status/error text, responsive layouts, and reduced-motion rules are present in the code.

These are implementation measures, not a completed usability study. Mobile preview readability and small-screen interaction issues were reported during prototyping and remain unverified. Complete the keyboard, mobile, and readability steps in the QA checklist and record any issues before submission.

## Security and privacy

- Account and portfolio-owner checks protect management and export routes.
- Supabase access tokens are verified server-side before a Laravel session is established.
- Requests that change data use Laravel's CSRF protection and route throttles.
- The Supabase service key and database credentials belong only in private server environment settings.
- Keep test data separate from real portfolio content; use a disposable portfolio for acceptance testing.

## Submission checklist

- [x] Published website URL is set.
- [x] Source repository URL is set.
- [x] Database platform and hosting platform are named.
- [x] Project description and technology stack are documented.
- [x] Logical database entities and relationships are documented.
- [x] Trash, restore, permanent-delete behavior, and the checked Supabase schema snapshot are documented.
- [ ] Add reproducible migrations or a safe schema setup script for all portfolio-domain tables.
- [x] Add desktop screenshots of Simple (light and dark), Modern, and Creative using synthetic demo content.
- [x] Add a screenshot of the email confirmation landing page.
- [ ] Confirm the instructor can sign in and test the deployed app.
- [ ] Complete and sign off the remaining manual QA cases.
- [ ] Confirm no credentials or private database values are included in the submission.
