# PortFold Prototype and Issue Log

This is a retrospective log of the feedback and defects reported while developing PortFold. Individual prototype dates were not recorded, so entries are ordered by development stage rather than assigned invented dates. The log separates user-reported observations from checks performed in the current workspace.

## Prototype iterations

| Stage | Feedback / design decision | Current evidence |
| --- | --- | --- |
| Portfolio editor | Make Create feel like Edit and apply HCI principles; reduce free-form phone entry by letting people choose a country calling code and enter the local number. | Create and Edit use the same country-code and phone-number approach. Recheck both flows manually on mobile before release. |
| Template differentiation | Keep the templates visually distinct: Simple uses glassmorphism in both themes; Modern uses a neomorphic, terminal-inspired surface; Creative keeps its cosmic Three.js black-hole scene and floating skill orbit. | Current screenshots are in `screenshots/simple-light.jpg`, `screenshots/simple-dark.jpg`, `screenshots/modern.jpg`, and `screenshots/creative.jpg`. The captures use synthetic data. |
| Creative fidelity | Early Creative iterations did not match the reference black hole; the profile image also went missing, and an intermediate result showed the wrong image in the orbit center. | The current local Creative capture shows the black-hole scene, a profile avatar in the orbit center, and floating skill tiles. Compare the scene with the product reference during final visual sign-off. |
| Project editing | Adding a project during Edit was reported to prevent saving. | The issue was addressed in commit `f0d59bc` (“Fix project saves from portfolio editor”). Existing automated regression coverage passes in the current suite (13 tests, 82 assertions). A deployed add-project/save/refresh check is still required. |
| Account confirmation | Inline “check your inbox” feedback was too easy to miss as a registration outcome. | Registration without an active Supabase session now opens a dedicated confirmation landing page with next steps, resend, sign-in, and different-email actions. Feature test and local screenshot are checked in. Actual email delivery and link completion depend on Supabase settings and still need live verification. |
| Documentation handoff | README and docs were present only in the local checkout, so GitHub did not show them. | This commit adds the README links and screenshot evidence to the repository; confirm the pushed commit appears on the remote default branch. |

## Reported errors and current status

| Reported symptom | Current status | Follow-up needed |
| --- | --- | --- |
| Google sign-in initially returned `Unsupported provider: provider is not enabled`. | Supabase's Google provider was later enabled/configured during testing, but a successful completed login has not been verified from the evidence available here. | Re-test with a listed OAuth test account; verify Google Cloud client ID/secret, Supabase callback, Site URL, redirect allow-list, and test-user audience. Never commit the client secret. |
| OAuth returned to `/auth/callback` and the app displayed “We could not verify your sign-in”; the browser network panel showed a 401 response from the Supabase `token?grant_type=pkce` request. | Root cause and resolution are not confirmed. Treat Google OAuth as unverified, even if provider credentials have since been entered. | Reproduce on the deployed site and record the sanitized HTTP status / Supabase error. Check that the same PKCE verifier survives the provider redirect and the deployed callback host matches the allowed redirect. Do not publish authorization codes, tokens, or verifier values. |
| Local OAuth callback opened `localhost:3000` even though PortFold runs on port 8000; later testing also reached `localhost:3000` with connection refused. | A stale/wrong OAuth redirect configuration was reported. A later user-approved update set the Supabase Site URL to `https://portfold.onrender.com` and allowed `/auth/callback`; the deployed flow still needs a fresh end-to-end test. | Start sign-in from `https://portfold.onrender.com`; check the URL after Google returns and verify the final authenticated page. Avoid relying on old callback URLs from earlier attempts. |
| Adding a project to an existing portfolio appeared to block Save. | Code fix is present in `f0d59bc`; automated regression tests pass. | Verify with a disposable deployed portfolio, refresh after saving, and delete the disposable record afterward. |
| Latest Render deploy timed out at 15m31s. | The dashboard health-check path was blank while `render.yaml` declared `/up`. I set the dashboard path to `/up`; commit `393a6a9` then deployed in 46.6 seconds and the health endpoint returned HTTP 200. | Keep `/up` in both the dashboard and `render.yaml`; recheck it after future service changes. |
| Build completed with optional dependency and bundle-size warnings. | `npm run build` succeeds. Vite warned that optional `fontaine` was absent and the Creative hero chunk is above 500 kB minified. | Check whether `fontaine` is required for deployment. Consider splitting/lazy-loading the Creative scene if first-load performance is poor, and check on a constrained connection/device. |
| Fresh database setup is not fully reproducible from tracked migrations. | Still open: the app expects pre-existing portfolio-domain tables in Supabase; tracked migrations do not create all of them. | Add reviewed, versioned migrations or a sanitized schema setup before claiming a clean database can be provisioned from source. Do not export production rows or secrets. |

## Prototype artifacts

The README embeds the current local screenshots. They are visual snapshots of the current state, not a complete archive of every intermediate code revision. The original design-reference pictures and real-account browser screenshots are intentionally not copied into the repository; the checked-in captures use synthetic names, reserved example addresses, and a separate disposable local fixture.

For source-code prototype history, use Git commit history and branches. Do not treat screenshots as proof that OAuth, deployed writes, data ownership, or responsive/accessibility behavior has passed acceptance testing.

## Release blockers to close

1. Complete a deployed Google sign-in and sign-out using an authorized test account.
2. Verify the live confirmation email is delivered, opens the expected callback, and establishes the session.
3. Verify deployed create, add-project/save/refresh, image upload, JSON/HTML export, and delete using disposable data.
4. Resolve or document how a fresh Supabase database receives all portfolio-domain tables and constraints.
5. Complete keyboard, mobile viewport, reduced-motion, contrast, and independent usability checks listed in `QA-CHECKLIST.md`.
