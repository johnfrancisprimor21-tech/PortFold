# PortFold QA Checklist

Use this checklist before a class demonstration or release. **Pending** means the check has not been verified yet; it does not by itself mean the feature is broken. Use a dedicated test account and disposable portfolio for any live write/delete checks. Do not use a real user's portfolio as test data.

## Verification already completed

Checked on 2026-10-05:

- [x] `php artisan test` — **13 tests passed, 82 assertions** after adding coverage for the email-confirmation page.
- [x] `npm run build` — completed successfully. Vite reports the optional `fontaine` package is unavailable and warns that the Creative hero JS chunk exceeds 500 kB minified; neither warning stopped the build.
- [x] `GET https://portfold.onrender.com/` — HTTP 200.
- [x] After pushing commit `32545bd`, opened `https://portfold.onrender.com/email/confirmation` in a browser; the deployed page rendered with the inbox instructions, resend control, sign-in link, and different-email link.
- [x] `GET https://portfold.onrender.com/login` — HTTP 200.
- [x] `GET https://portfold.onrender.com/up` — HTTP 200.
- [x] Render health-check configuration now uses `/up`, matching `render.yaml`. The retry for commit `393a6a9` deployed successfully in 46.6 seconds; Render's internal health request to `/up` returned HTTP 200.
- [x] After that deploy, read-only GET checks for `/`, `/up`, `/login`, `/register`, and `/email/confirmation` returned HTTP 200.
- [x] Guest request to `/portfolio/create` — HTTP 302, confirming the authenticated route redirects a signed-out visitor to sign-in.
- [x] Confirmed the repository has three template views and the seeder entries are Simple, Modern, and Creative.
- [x] Captured and visually reviewed synthetic-data screenshots for Simple light/dark, Modern, Creative, and the email-confirmation landing page; files are in `docs/screenshots/`.
- [x] Added a dedicated email-confirmation landing page with next steps, a resend action, and navigation back to sign-in or registration. Automated feature test confirms the route and key controls render.

The automated suite covers Supabase token/session behavior and edit-form/project-save regressions. The production checks above were read-only. They do not prove Google OAuth completion, email delivery/resend, live Supabase writes, image uploads, or every public portfolio view.

The UI screenshots are local prototype evidence with fictional demo data. They do not prove mobile breakpoints, keyboard-only operation, contrast, or real production data flow; those remain manual checks below.

## Manual release checks

### Account and access

- [ ] Open the deployed sign-in page in a clean/private browser session.
- [ ] Sign in with the designated test Google account.
- [ ] If Google OAuth is in Testing mode, confirm that account is listed as an OAuth test user.
- [ ] Confirm successful sign-in reaches the account's Manage page.
- [ ] Sign out; confirm protected create/manage/edit/export routes return to sign-in.
- [ ] Confirm a signed-in account cannot edit or delete another account's portfolio.

### Create and persistence

- [ ] With the test account, create a disposable portfolio using clearly synthetic contact details.
- [ ] Save name, headline, email, phone/country code, location, biography, and a test profile image.
- [ ] Add several skills, one education entry, one experience entry, one project, and one social link.
- [ ] Confirm the app advances to template selection and reports save success.
- [ ] Select each of Simple, Modern, and Creative in turn; generate and preview each design.
- [ ] Open the public portfolio URL in a signed-out browser window; confirm it renders without exposing management controls.
- [ ] Refresh the public page and Manage page; confirm saved fields and selected template remain.
- [ ] Confirm the profile and project images load from Supabase Storage.

### Edit, delete, and exports

- [ ] Open the disposable portfolio in Edit.
- [ ] Add a project, save, and confirm the new project appears after redirect and refresh.
- [ ] Trigger a validation error; confirm the form identifies the field and retains entered project text.
- [ ] Download JSON; confirm it is valid JSON and includes the expected profile, skills, projects, education, experience, links, and selected template.
- [ ] Download HTML; open the file locally and verify the content. Note whether remote fonts, icons, images, or other assets require an internet connection.
- [ ] Delete the disposable portfolio and verify it disappears from Manage and its public URL no longer displays it.

### Usability and HCI

- [ ] Test at phone width, tablet width, and desktop width; confirm no horizontal overflow, clipped controls, or unreadable text.
- [ ] Complete create, template selection, and edit using only the keyboard; check focus order and visible focus indication.
- [ ] Confirm inputs have clear labels, required fields are identified, and validation messages explain how to fix errors.
- [ ] Confirm success, error, and loading states are understandable and do not lose the user's work.
- [ ] Enable reduced motion in the operating system/browser and confirm decorative animations are reduced or disabled.
- [ ] Verify text contrast and zoomed text remain readable in both supported themes.
- [ ] Ask one person unfamiliar with the app to create and preview a portfolio; record where they hesitate or make an error.

### Database and deployment

- [ ] Confirm the deployed app uses Supabase PostgreSQL, not a local SQLite database.
- [ ] Verify the actual Supabase table and constraint metadata against the logical model in `PROJECT-DOCUMENTATION.md`.
- [ ] Add migrations or a safe schema setup procedure that can provision the portfolio-domain tables on a fresh database.
- [ ] Confirm the Simple, Modern, and Creative template records exist exactly once in the configured database.
- [x] Confirmed at 11:50 on 2026-10-05: Render marked deployment `393a6a9` live, and the `/up` HTTP health check passed. Retest after any future deployment.
- [ ] Confirm Laravel production settings keep debug output off and HTTPS cookies enabled.
- [ ] Confirm no database password, `APP_KEY`, Supabase service key, OAuth secret, or personal test data is committed or included in the submission.

## Automated checks

Run from the repository root:

```powershell
php artisan test
npm run build
```

Record the date, commit, results, and any failures here before the final submission:

| Date | Commit | Automated test result | Build result | Tester / notes |
| --- | --- | --- | --- | --- |
| 2026-10-05 | commit `393a6a9` (deployed) | 13 passed; 82 assertions in the prior run; not rerun for documentation-only changes | `npm run build` succeeded in the prior run; optional fontaine and large Creative chunk warnings | Render health path aligned to `/up`; retry deployed in 46.6s. External GET smoke checks for `/`, `/up`, `/login`, `/register`, and `/email/confirmation` returned HTTP 200. Template and confirmation screenshots use synthetic content. |

## Final sign-off

- [ ] All manual cases above are complete or explicitly accepted as out of scope by the instructor.
- [ ] No known high-impact failures remain.
- [x] The checked-in screenshots use synthetic demo data and do not reveal private contact details.
- [x] Submission artifacts are present: source repository, live URL, documentation, platform details, and screenshots of all three templates.

**Sign-off name:** ____________________
**Date:** ____________________
**Notes:** ______________________________________________________________
