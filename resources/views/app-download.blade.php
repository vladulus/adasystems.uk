@extends('layouts.public')

@section('title', 'Download Android App - ADA Systems Fleet Management')
@section('description', 'Download the ADA Systems Android app for real-time fleet monitoring. GPS tracking, OBD-II diagnostics, and live vehicle data on your mobile device.')
@section('keywords', 'ADA Systems app, fleet management app, Android fleet tracking, vehicle monitoring app, OBD-II app, GPS tracking mobile')
@section('og_type', 'product')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MobileApplication",
    "name": "ADA Systems",
    "operatingSystem": "Android",
    "applicationCategory": "BusinessApplication",
    "description": "Fleet management dashboard app for real-time vehicle monitoring, GPS tracking, and OBD-II diagnostics.",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "GBP"
    },
    "author": {
        "@type": "Organization",
        "name": "ADA Systems",
        "url": "https://adasystems.uk"
    },
    "softwareVersion": "1.0.0",
    "fileSize": "5MB",
    "downloadUrl": "{{ asset('download/ada-systems.apk') }}",
    "screenshot": "{{ asset('logo.png') }}",
    "featureList": "Live Dashboard, GPS Tracking, Push Notifications, OBD-II Data"
}
</script>
@endsection

@section('content')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

<div class="download-page">
    <div class="download-card">
        {{-- Header --}}
        <div class="download-header">
            <h1>ADA Systems App</h1>
            <p>Fleet management dashboard on your phone</p>
        </div>

        {{-- QR Code --}}
        <div class="qr-wrapper">
            <div class="qr-container">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=H&data={{ urlencode(url('/app')) }}&color=0f172a" alt="QR Code">
            </div>
            <div class="qr-logo">
                <img src="{{ asset('logo.png') }}" alt="ADA">
            </div>
        </div>

        <p class="scan-text">Scan to download</p>

        {{-- Download Button --}}
        <a href="{{ asset('download/ada-systems.apk') }}" class="btn-download" download="ADA-Systems.apk">
            <i class="fab fa-android"></i>
            <span>Download APK</span>
        </a>

        {{-- Version Info --}}
        <div class="version-info">
            <span>Version 1.0.0</span>
            <span class="separator">•</span>
            <span>Android 7.0+</span>
        </div>

        {{-- Features --}}
        <div class="features-row">
            <div class="feature">
                <i class="fas fa-tachometer-alt"></i>
                <span>Live Dashboard</span>
            </div>
            <div class="feature">
                <i class="fas fa-map-marker-alt"></i>
                <span>GPS Tracking</span>
            </div>
            <div class="feature">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </div>
        </div>
    </div>

    {{-- Install Instructions --}}
    <div class="install-steps">
        <div class="install-title">
            <i class="fas fa-info-circle"></i>
            <span>How to install</span>
        </div>
        <ol class="steps-list">
            <li>Tap <strong>Download APK</strong> above</li>
            <li>Open the downloaded file from notifications or Downloads</li>
            <li>If prompted, allow installation from this source</li>
            <li>Tap <strong>Install</strong> and you're done!</li>
        </ol>
    </div>
</div>

<style>
    .download-page {
        min-height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }

    .download-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 40px;
        text-align: center;
        box-shadow: 
            0 25px 50px rgba(124, 58, 237, 0.15),
            0 0 0 1px rgba(148, 163, 184, 0.1);
        max-width: 400px;
        width: 100%;
    }

    .download-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px 0;
    }

    .download-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0 0 32px 0;
    }

    /* QR Code Container */
    .qr-wrapper {
        position: relative;
        display: inline-block;
        padding: 16px;
        background: #ffffff;
        border-radius: 16px;
        border: 2px solid #e2e8f0;
    }

    .qr-container {
        width: 200px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qr-container img {
        width: 100%;
        height: 100%;
        border-radius: 4px;
    }

    .qr-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50px;
        height: 50px;
        background: #ffffff;
        border-radius: 10px;
        padding: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qr-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .scan-text {
        margin: 20px 0 24px;
        font-size: 13px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Download Button */
    .btn-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #ffffff;
        padding: 14px 32px;
        border-radius: 999px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.35);
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(34, 197, 94, 0.4);
    }

    .btn-download i {
        font-size: 20px;
    }

    /* Version Info */
    .version-info {
        margin-top: 20px;
        font-size: 12px;
        color: #94a3b8;
    }

    .version-info .separator {
        margin: 0 8px;
    }

    /* Features */
    .features-row {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
    }

    .feature {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .feature i {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 10px;
        color: #6366f1;
        font-size: 16px;
    }

    .feature span {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    /* Install Note */
    .install-steps {
        margin-top: 24px;
        padding: 16px 20px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        max-width: 400px;
        text-align: left;
    }

    .install-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 10px;
    }

    .install-title i {
        color: #f59e0b;
    }

    .steps-list {
        margin: 0;
        padding-left: 20px;
        font-size: 12px;
        color: #78350f;
        line-height: 1.8;
    }

    .steps-list li {
        padding-left: 4px;
    }

    .steps-list strong {
        color: #92400e;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .download-card {
            padding: 32px 24px;
        }

        .download-header h1 {
            font-size: 20px;
        }

        .qr-container {
            width: 180px;
            height: 180px;
        }

        .qr-logo {
            width: 44px;
            height: 44px;
        }

        .features-row {
            gap: 16px;
        }

        .feature i {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .feature span {
            font-size: 11px;
        }

        .btn-download {
            padding: 12px 28px;
            font-size: 15px;
        }
    }
</style>
@endsection

@section('scripts')
{{-- No scripts needed - QR generated via API --}}
@endsection