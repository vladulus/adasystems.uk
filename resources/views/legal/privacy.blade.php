@extends('legal.layout')

@section('title', 'Privacy Policy - ADASystems Fleet Management | GDPR Compliant')
@section('description', 'ADASystems Privacy Policy explains how we collect, use, and protect your personal data. GDPR compliant fleet management data processing for vehicle tracking and driver monitoring.')
@section('keywords', 'ADASystems privacy policy, GDPR fleet management, data protection, vehicle tracking privacy, driver data protection, UK GDPR compliance')
@section('page_title', 'Privacy Policy')
@section('page_subtitle', 'How We Collect, Use and Protect Your Data')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Privacy Policy - ADASystems",
    "description": "Privacy Policy for ADASystems fleet management services - GDPR compliant",
    "url": "{{ url()->current() }}"
}
</script>
@endsection

@section('legal_content')
<h2>1. Introduction</h2>
<p>ADASystems ("we", "us", "our") is committed to protecting and respecting your privacy. This Privacy Policy explains how we collect, use, store, and protect your personal data when you use our fleet management services, website (adasystems.uk), and Devices.</p>
<p>We are the data controller for the personal data we process. We are registered in England and Wales and operate from Birmingham, United Kingdom.</p>

<h2>2. Data We Collect</h2>
<h3>2.1 Account Information</h3>
<ul>
    <li>Name, email address, phone number</li>
    <li>Company name and business address</li>
    <li>Billing and payment information</li>
    <li>User credentials and authentication data</li>
</ul>

<h3>2.2 Vehicle and Device Data</h3>
<ul>
    <li>GPS location data and route history</li>
    <li>Vehicle speed, acceleration, and braking patterns</li>
    <li>Engine diagnostics (OBD-II data) including fault codes, fuel consumption, mileage</li>
    <li>Tachograph data including driving hours and rest periods</li>
    <li>Device status, connectivity, and health metrics</li>
</ul>

<h3>2.3 Driver Data</h3>
<ul>
    <li>Driver identification and assignment records</li>
    <li>Driving behaviour scores and analytics</li>
    <li>Working time records</li>
</ul>

<h3>2.4 Website Data</h3>
<ul>
    <li>IP address and browser information</li>
    <li>Cookies and usage analytics</li>
    <li>Communication records and support tickets</li>
</ul>

<h2>3. Legal Basis for Processing</h2>
<p>We process your personal data under the following legal bases:</p>
<ul>
    <li><strong>Contract Performance:</strong> To provide our fleet management services as agreed.</li>
    <li><strong>Legal Obligation:</strong> To comply with tachograph regulations, tax requirements, and other legal obligations.</li>
    <li><strong>Legitimate Interests:</strong> For fraud prevention, security, service improvement, and business analytics.</li>
    <li><strong>Consent:</strong> For marketing communications and cookies (where applicable).</li>
</ul>

<h2>4. How We Use Your Data</h2>
<ul>
    <li>Providing and operating the fleet management platform</li>
    <li>Real-time vehicle tracking and monitoring</li>
    <li>Generating reports and analytics for customers</li>
    <li>Supporting EU tachograph compliance</li>
    <li>Billing and account management</li>
    <li>Customer support and communication</li>
    <li>Device maintenance, updates, and security</li>
    <li>Service improvement and development</li>
</ul>

<h2>5. Data Sharing</h2>
<p>We may share your data with:</p>
<ul>
    <li><strong>Service Providers:</strong> Cloud hosting, payment processors, and IT support providers who assist in delivering our services.</li>
    <li><strong>Legal Authorities:</strong> When required by law, court order, or regulatory obligation.</li>
    <li><strong>Your Employer:</strong> If you are a driver, your employer (our customer) has access to vehicle and driving data.</li>
</ul>
<p>We do not sell your personal data to third parties.</p>

<h2>6. Data Retention</h2>
<ul>
    <li>Account data: Retained for the duration of the contract plus 6 years</li>
    <li>Vehicle telemetry: Retained for 2 years (configurable by customer)</li>
    <li>Tachograph data: Retained as required by EU regulations (minimum 1 year)</li>
    <li>Billing records: Retained for 7 years for tax compliance</li>
</ul>

<h2>7. Your Rights</h2>
<p>Under UK GDPR, you have the right to:</p>
<ul>
    <li><strong>Access:</strong> Request a copy of your personal data</li>
    <li><strong>Rectification:</strong> Correct inaccurate or incomplete data</li>
    <li><strong>Erasure:</strong> Request deletion of your data (subject to legal obligations)</li>
    <li><strong>Restriction:</strong> Limit how we process your data</li>
    <li><strong>Portability:</strong> Receive your data in a portable format</li>
    <li><strong>Object:</strong> Object to processing based on legitimate interests</li>
</ul>
<p>To exercise your rights, contact us at <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a>.</p>

<h2>8. Data Security</h2>
<p>We implement appropriate technical and organisational measures to protect your data, including encryption in transit and at rest, secure authentication, access controls, regular security audits, and secure data centres.</p>

<h2>9. International Data Transfers</h2>
<p>Your data is primarily processed within the UK and EEA. Where transfers outside these areas are necessary, we ensure appropriate safeguards are in place, such as Standard Contractual Clauses.</p>

<h2>10. Changes to This Policy</h2>
<p>We may update this Privacy Policy periodically. We will notify you of significant changes via email or platform notification.</p>

<h2>11. Contact Us</h2>
<p>For privacy-related enquiries or to exercise your rights:</p>
<p><strong>ADASystems</strong><br>Birmingham, United Kingdom<br>Email: <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a></p>
<p>You also have the right to lodge a complaint with the Information Commissioner's Office (ICO) at <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a>.</p>
@endsection
