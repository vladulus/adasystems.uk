@extends('layouts.public')

@section('og_type', 'article')

@section('styles')
.legal-hero { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 4rem 0; text-align: center; }
.legal-hero h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
.legal-hero .subtitle { font-size: 1.1rem; opacity: 0.9; }
.legal-content { padding: 3rem 0; }
.legal-container { max-width: 900px; margin: 0 auto; }
.meta-info { background: var(--bg-light); padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; display: flex; gap: 2rem; flex-wrap: wrap; }
.meta-info span { color: var(--text-gray); font-size: 0.9rem; }
.meta-info strong { color: var(--text-dark); }
.legal-container h2 { font-size: 1.5rem; color: var(--primary-dark); margin-top: 2.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--border); }
.legal-container h3 { font-size: 1.2rem; color: var(--text-dark); margin-top: 1.5rem; margin-bottom: 0.75rem; }
.legal-container p { margin-bottom: 1rem; color: var(--text-gray); line-height: 1.8; }
.legal-container ul, .legal-container ol { margin-bottom: 1rem; padding-left: 1.5rem; color: var(--text-gray); }
.legal-container li { margin-bottom: 0.5rem; line-height: 1.7; }
.legal-container strong { color: var(--text-dark); }
.highlight-box { background: #fef3c7; padding: 1rem 1.5rem; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 1.5rem 0; }
.highlight-box p { margin: 0; color: var(--text-dark); }
.table-container { overflow-x: auto; margin: 1.5rem 0; }
.legal-container table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.legal-container th, .legal-container td { padding: 0.75rem 1rem; text-align: left; border: 1px solid var(--border); }
.legal-container th { background: var(--primary-dark); color: white; font-weight: 600; }
.legal-container tr:nth-child(even) { background: var(--bg-light); }
@media (max-width: 768px) { .legal-hero h1 { font-size: 1.8rem; } .meta-info { flex-direction: column; gap: 0.5rem; } }
@endsection

@section('content')
<section class="legal-hero">
    <div class="container">
        <h1>@yield('page_title')</h1>
        <p class="subtitle">@yield('page_subtitle', 'ADASystems Legal Documentation')</p>
    </div>
</section>

<section class="legal-content">
    <div class="container">
        <div class="legal-container">
            <div class="meta-info">
                <span><strong>Effective Date:</strong> December 2024</span>
                <span><strong>Last Updated:</strong> December 2024</span>
            </div>
            @yield('legal_content')
        </div>
    </div>
</section>
@endsection
