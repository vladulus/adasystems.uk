@extends('layouts.public')

@section('title', 'Contact ADASystems - Get Fleet Management Solutions | Request Demo')
@section('description', 'Contact ADASystems for IoT telemetry and fleet management solutions. Request a demo, get technical support, or inquire about custom fleet tracking systems. Birmingham, UK.')
@section('keywords', 'contact ADASystems, fleet management inquiry, IoT telemetry demo, vehicle tracking support, OBD-II solutions contact, fleet management quote, Birmingham UK')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "Contact ADASystems",
    "description": "Get in touch with ADASystems for fleet management solutions",
    "url": "{{ url('/contact') }}",
    "mainEntity": {
        "@type": "Organization",
        "name": "ADASystems",
        "email": "hello@adasystems.uk",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Birmingham",
            "addressCountry": "GB"
        }
    }
}
</script>
@endsection

@section('styles')
.contact-hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4rem 0; text-align: center; }
.contact-hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; }
.contact-hero p { font-size: 1.25rem; opacity: 0.95; }
.contact-section { padding: 4rem 0; background: var(--bg-light); }
.contact-container { max-width: 800px; margin: 0 auto; background: var(--bg-white); border-radius: 16px; padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark); }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.875rem; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 1rem; transition: all 0.3s; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.form-group textarea { min-height: 150px; resize: vertical; }
.required { color: var(--error); }
.submit-button { background: var(--primary); color: white; padding: 1rem 2.5rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; }
.submit-button:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
.alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
.contact-info { margin-top: 3rem; padding-top: 3rem; border-top: 1px solid var(--border); }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem; }
.info-item { text-align: center; }
.info-icon { width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; }
.info-item h3 { font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--text-dark); }
.info-item p { color: var(--text-gray); }
.info-item a { color: var(--primary); text-decoration: none; }
.info-item a:hover { text-decoration: underline; }
@media (max-width: 768px) { .contact-hero h1 { font-size: 2rem; } .contact-container { padding: 2rem 1.5rem; } }
@endsection

@section('content')
<section class="contact-hero">
    <div class="container">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label for="company">Company Name</label>
                    <input type="text" id="company" name="company" value="{{ old('company') }}">
                </div>
                <div class="form-group">
                    <label for="subject">Subject <span class="required">*</span></label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                        <option value="Product Demo" {{ old('subject') == 'Product Demo' ? 'selected' : '' }}>Product Demo Request</option>
                        <option value="Technical Support" {{ old('subject') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                        <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership Opportunity</option>
                        <option value="Custom Solution" {{ old('subject') == 'Custom Solution' ? 'selected' : '' }}>Custom Solution Inquiry</option>
                        <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="submit-button">Send Message</button>
            </form>

            <div class="contact-info">
                <h2 style="text-align: center; margin-bottom: 1rem;">Other Ways to Reach Us</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">📧</div>
                        <h3>Email</h3>
                        <p><a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a></p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <h3>Location</h3>
                        <p>Birmingham<br>United Kingdom</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">⏰</div>
                        <h3>Business Hours</h3>
                        <p>Monday - Friday<br>9:00 AM - 5:00 PM GMT</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
