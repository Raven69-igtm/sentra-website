<div class="footer">
    <div class="copyright text-center">
        <p class="mb-0 text-muted" style="font-size: 0.82rem;">
            <strong class="text-dark">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</strong>
            &bull; {{ \App\Models\SystemSetting::get('footer_copyright', '© ' . date('Y') . ' UPTD PPA & Dinas Sosial PPPA. Seluruh hak cipta dilindungi.') }}
        </p>
    </div>
</div>
