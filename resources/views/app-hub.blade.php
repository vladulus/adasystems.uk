@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-wrapper" style="max-width:1100px;margin:0 auto;padding:24px 16px 40px;">
    <div style="margin-bottom:20px;">
        <h1 style="font-size:26px;font-weight:600;margin:0 0 4px;">
            Welcome, {{ auth()->user()->name }}!
        </h1>
        <p style="color:#6b7280;margin:0;">
            Choose where you want to go.
        </p>
    </div>

    <div style="
        display:grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap:18px;
    ">
        {{-- Card: Management --}}
        <a href="{{ route('management.index') }}" style="text-decoration:none;">
            <div style="
                background:#ffffff;
                border-radius:18px;
                border:1px solid rgba(148,163,184,0.35);
                box-shadow:
                    0 18px 45px rgba(124,58,237,0.16),
                    0 0 0 1px rgba(148,163,184,0.15);
                padding:20px 18px;
                display:flex;
                flex-direction:column;
                gap:10px;
                transition:transform .15s, box-shadow .15s;
            " onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 22px 55px rgba(79,70,229,0.26),0 0 0 1px rgba(148,163,184,0.2)';"
               onmouseout="this.style.transform='';this.style.boxShadow='0 18px 45px rgba(124,58,237,0.16),0 0 0 1px rgba(148,163,184,0.15)';">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="
                        width:40px;height:40px;border-radius:999px;
                        background:linear-gradient(135deg,#6366f1,#8b5cf6);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-size:18px;
                    ">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:16px;color:#111827;">
                            Management
                        </div>
                        <div style="font-size:13px;color:#6b7280;">
                            Manage devices, vehicles, users and drivers.
                        </div>
                    </div>
                </div>
                <div style="margin-top:8px;font-size:13px;color:#4b5563;">
                    Open the management dashboard with all tools and statistics.
                </div>
                <div style="margin-top:10px;">
                    <span style="
                        display:inline-flex;align-items:center;gap:6px;
                        padding:7px 12px;border-radius:999px;
                        background:#eef2ff;color:#4338ca;
                        font-size:12px;font-weight:500;
                    ">
                        Go to Management
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>

        {{-- Card: Pi Dashboard --}}
        <a href="{{ route('pi.dashboard') }}" style="text-decoration:none;">
            <div style="
                background:#ffffff;
                border-radius:18px;
                border:1px solid rgba(148,163,184,0.35);
                box-shadow:
                    0 18px 45px rgba(14,165,233,0.16),
                    0 0 0 1px rgba(148,163,184,0.15);
                padding:20px 18px;
                display:flex;
                flex-direction:column;
                gap:10px;
                transition:transform .15s, box-shadow .15s;
            " onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 22px 55px rgba(14,165,233,0.26),0 0 0 1px rgba(148,163,184,0.2)';"
               onmouseout="this.style.transform='';this.style.boxShadow='0 18px 45px rgba(14,165,233,0.16),0 0 0 1px rgba(148,163,184,0.15)';">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="
                        width:40px;height:40px;border-radius:999px;
                        background:linear-gradient(135deg,#0ea5e9,#22c55e);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-size:18px;
                    ">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:16px;color:#111827;">
                            Pi Dashboard
                        </div>
                        <div style="font-size:13px;color:#6b7280;">
                            Live data from ADA-Pi devices (GPS, OBD, system, logs).
                        </div>
                    </div>
                </div>
                <div style="margin-top:8px;font-size:13px;color:#4b5563;">
                    Open the Pi control panel for this user’s assigned devices.
                </div>
                <div style="margin-top:10px;">
                    <span style="
                        display:inline-flex;align-items:center;gap:6px;
                        padding:7px 12px;border-radius:999px;
                        background:#ecfeff;color:#0369a1;
                        font-size:12px;font-weight:500;
                    ">
                        Go to Pi dashboard
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
