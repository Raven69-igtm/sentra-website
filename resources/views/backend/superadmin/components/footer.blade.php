<div class="footer" style="background: #FFFFFF; border-top: 1px solid #EBECEF;">
    <div class="copyright text-center py-3">
        <p class="mb-0 text-muted" style="font-size: 0.82rem;">
            <strong class="text-dark">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }} Superadmin Console</strong>
            &bull; {{ \App\Models\SystemSetting::get('footer_copyright', '© ' . date('Y') . ' UPT PPA & Dinas Sosial. Seluruh hak cipta dilindungi.') }}
        </p>
    </div>
</div>
