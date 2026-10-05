import { createClient } from '@supabase/supabase-js';

const configuration = document.querySelector('[data-supabase-auth-config]');
const resetAuthorizationPresent = window.location.hash.length > 0
    || new URLSearchParams(window.location.search).has('code');

if (configuration) {
    const statusMessage = document.querySelector('[data-auth-status]');
    let supabaseClient;

    function setStatus(message, type = 'status') {
        if (!statusMessage) {
            return;
        }

        statusMessage.textContent = message;
        statusMessage.hidden = !message;
        statusMessage.setAttribute('role', type);
    }

    function getClient() {
        if (supabaseClient) {
            return supabaseClient;
        }

        const projectUrl = configuration.dataset.url;
        const publicKey = configuration.dataset.anonKey;

        if (!projectUrl || !publicKey) {
            throw new Error('Account sign-in is not configured yet. Please try again later.');
        }

        supabaseClient = createClient(projectUrl, publicKey, {
            auth: {
                flowType: 'pkce',
                detectSessionInUrl: true,
                persistSession: true,
                autoRefreshToken: true,
            },
        });

        return supabaseClient;
    }

    function errorMessage(error, fallback) {
        const message = String(error?.message || '').toLowerCase();

        if (message.includes('invalid login credentials')) {
            return 'Email or password is incorrect. Check your details and try again.';
        }

        if (message.includes('email not confirmed')) {
            return 'Please confirm your email from the link we sent before signing in.';
        }

        if (message.includes('password')) {
            return 'Use a stronger password and try again.';
        }

        if (message.includes('rate limit') || message.includes('too many requests')) {
            return 'Too many attempts. Wait a little and try again.';
        }

        return fallback;
    }

    async function connectLaravelSession(session) {
        const response = await fetch(configuration.dataset.sessionUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                access_token: session.access_token,
                refresh_token: session.refresh_token,
                expires_in: session.expires_in,
            }),
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(result.message || 'We could not prepare your account session. Please try again.');
        }

        await getClient().auth.signOut({ scope: 'local' });
        window.location.assign(result.redirect || configuration.dataset.manageUrl);
    }

    function showFormBusy(form, busy) {
        const button = form.querySelector('[type="submit"]');
        if (!button) {
            return;
        }

        button.disabled = busy;
        button.setAttribute('aria-busy', busy ? 'true' : 'false');
        if (busy && button.dataset.busyText) {
            button.dataset.originalText = button.dataset.originalText || button.textContent;
            button.textContent = button.dataset.busyText;
        } else if (!busy && button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
            button.removeAttribute('aria-busy');
        }
    }

    const resendConfirmationButton = document.querySelector('[data-resend-confirmation]');
    if (resendConfirmationButton) {
        const pendingEmail = sessionStorage.getItem('portfold.pending-email') || '';
        const emailDisplay = document.querySelector('[data-confirmation-email]');
        const confirmationStatus = document.querySelector('[data-confirmation-status]');

        if (emailDisplay && pendingEmail) {
            const [localPart, domain] = pendingEmail.split('@');
            const maskedLocalPart = localPart.length > 2
                ? `${localPart.slice(0, 2)}${'•'.repeat(Math.min(localPart.length - 2, 8))}`
                : `${localPart.slice(0, 1)}•`;
            emailDisplay.textContent = domain ? `${maskedLocalPart}@${domain}` : 'your email address';
        }

        resendConfirmationButton.addEventListener('click', async () => {
            if (!pendingEmail) {
                confirmationStatus.textContent = 'Return to create account and enter your email address to request a new link.';
                confirmationStatus.hidden = false;
                return;
            }

            resendConfirmationButton.disabled = true;
            resendConfirmationButton.setAttribute('aria-busy', 'true');
            confirmationStatus.hidden = true;

            try {
                const { error } = await getClient().auth.resend({
                    type: 'signup',
                    email: pendingEmail,
                    options: { emailRedirectTo: configuration.dataset.callbackUrl },
                });

                if (error) {
                    throw error;
                }

                confirmationStatus.textContent = 'A new confirmation link is on its way. Check your inbox and spam folder.';
                confirmationStatus.dataset.kind = 'success';
            } catch (error) {
                confirmationStatus.textContent = errorMessage(error, 'We could not resend the confirmation email. Wait a moment and try again.');
                confirmationStatus.dataset.kind = 'error';
            } finally {
                confirmationStatus.hidden = false;
                resendConfirmationButton.disabled = false;
                resendConfirmationButton.removeAttribute('aria-busy');
            }
        });
    }

    document.querySelectorAll('[data-supabase-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            setStatus('');
            showFormBusy(form, true);

            try {
                const client = getClient();
                const type = form.dataset.supabaseForm;
                const email = form.querySelector('[name="email"]')?.value.trim();

                if (type === 'login') {
                    const password = form.querySelector('[name="password"]')?.value || '';
                    const { data, error } = await client.auth.signInWithPassword({ email, password });

                    if (error) {
                        setStatus(errorMessage(error, 'We could not sign you in. Check your details and try again.'), 'alert');
                        return;
                    }

                    if (data.session) {
                        await connectLaravelSession(data.session);
                    }
                }

                if (type === 'register') {
                    const password = form.querySelector('[name="password"]')?.value || '';
                    const confirmation = form.querySelector('[name="password_confirmation"]')?.value || '';
                    const fullName = form.querySelector('[name="name"]')?.value.trim() || '';

                    if (password !== confirmation) {
                        setStatus('The passwords do not match.', 'alert');
                        return;
                    }

                    if (password.length < 12) {
                        setStatus('Use a password with at least 12 characters.', 'alert');
                        return;
                    }

                    const { data, error } = await client.auth.signUp({
                        email,
                        password,
                        options: {
                            data: { full_name: fullName },
                            emailRedirectTo: configuration.dataset.callbackUrl,
                        },
                    });

                    if (error) {
                        setStatus(errorMessage(error, 'We could not create your account with those details. Check them and try again.'), 'alert');
                        return;
                    }

                    if (data.session) {
                        await connectLaravelSession(data.session);
                    } else {
                        sessionStorage.setItem('portfold.pending-email', email);
                        window.location.assign(configuration.dataset.confirmationUrl);
                    }
                }

                if (type === 'forgot') {
                    const { error } = await client.auth.resetPasswordForEmail(email, {
                        redirectTo: configuration.dataset.resetUrl,
                    });

                    if (error) {
                        setStatus(errorMessage(error, 'We could not send the reset link right now. Please try again.'), 'alert');
                        return;
                    }

                    setStatus('If an account uses that email, a password reset link will be sent.');
                    form.reset();
                }

                if (type === 'reset') {
                    const password = form.querySelector('[name="password"]')?.value || '';
                    const confirmation = form.querySelector('[name="password_confirmation"]')?.value || '';

                    if (password.length < 12) {
                        setStatus('Use a password with at least 12 characters.', 'alert');
                        return;
                    }

                    if (password !== confirmation) {
                        setStatus('The passwords do not match.', 'alert');
                        return;
                    }

                    const { error } = await client.auth.updateUser({ password });
                    if (error) {
                        setStatus(errorMessage(error, 'We could not update your password. Request a new reset link and try again.'), 'alert');
                        return;
                    }

                    await client.auth.signOut({ scope: 'local' });
                    form.reset();
                    setStatus('Your password has been changed. You can now sign in.');
                }
            } catch (error) {
                setStatus(error.message || 'Account sign-in is temporarily unavailable. Please try again later.', 'alert');
            } finally {
                showFormBusy(form, false);
            }
        });
    });

    document.querySelectorAll('[data-supabase-google]').forEach((button) => {
        button.addEventListener('click', async () => {
            setStatus('');
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');

            try {
                const { error } = await getClient().auth.signInWithOAuth({
                    provider: 'google',
                    options: { redirectTo: configuration.dataset.callbackUrl },
                });

                if (error) {
                    setStatus(errorMessage(error, 'Google sign-in is unavailable right now. Try again or use email and password.'), 'alert');
                    button.disabled = false;
                    button.removeAttribute('aria-busy');
                }
            } catch (error) {
                setStatus(error.message || 'Google sign-in is not configured yet.', 'alert');
                button.disabled = false;
                button.removeAttribute('aria-busy');
            }
        });
    });

    if (window.location.pathname === new URL(configuration.dataset.callbackUrl).pathname) {
        (async () => {
            try {
                const { data, error } = await getClient().auth.getSession();
                if (error || !data.session) {
                    setStatus('Sign-in could not be completed. Return to the sign-in page and try again.', 'alert');
                    return;
                }

                await connectLaravelSession(data.session);
            } catch (error) {
                setStatus(error.message || 'Sign-in could not be completed. Please try again.', 'alert');
            }
        })();
    }

    if (document.querySelector('[data-supabase-form="reset"]')) {
        (async () => {
            try {
                const { data, error } = await getClient().auth.getSession();
                const resetForm = document.querySelector('[data-supabase-form="reset"]');

                if (error || !data.session || !resetAuthorizationPresent) {
                    setStatus('This reset link is invalid or has expired. Request a new reset link and try again.', 'alert');
                    return;
                }

                resetForm.querySelectorAll('input, button').forEach((element) => {
                    element.disabled = false;
                });
                setStatus('Choose a new password for your account.');
            } catch (error) {
                setStatus(error.message || 'We could not verify this reset link.', 'alert');
            }
        })();
    }
}
