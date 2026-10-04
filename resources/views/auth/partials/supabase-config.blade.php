<div
    hidden
    data-supabase-auth-config
    data-url="{{ config('services.supabase.url') }}"
    data-anon-key="{{ config('services.supabase.anon_key') }}"
    data-session-url="{{ route('auth.supabase.session') }}"
    data-callback-url="{{ route('auth.callback') }}"
    data-reset-url="{{ route('password.reset') }}"
    data-manage-url="{{ route('portfolio.manage') }}"
></div>
