@extends('legal.layout')

@section('title', 'Terms and Conditions - ADASystems Fleet Management')
@section('description', 'Read the Terms and Conditions for ADASystems fleet management services. Understand device ownership, SaaS licensing, administrative rights, and service agreements for IoT telemetry solutions.')
@section('keywords', 'ADASystems terms conditions, fleet management terms, SaaS agreement, device ownership, IoT service agreement, UK terms of service')
@section('page_title', 'Terms and Conditions')
@section('page_subtitle', 'Service Agreement for ADASystems Fleet Management')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Terms and Conditions - ADASystems",
    "description": "Terms and Conditions for ADASystems fleet management services",
    "url": "{{ url()->current() }}",
    "publisher": { "@type": "Organization", "name": "ADASystems" }
}
</script>
@endsection

@section('legal_content')
<h2>1. Definitions and Interpretation</h2>
<p><strong>"ADASystems"</strong> or "we", "us", "our" refers to ADASystems, operating from Birmingham, United Kingdom.</p>
<p><strong>"Device"</strong> means the proprietary hardware unit supplied by ADASystems, featuring OBD-II vehicle diagnostics, GPS tracking, 4G/5G cellular connectivity, and EU tachograph integration capabilities.</p>
<p><strong>"Platform"</strong> means the ADASystems web-based fleet management software accessible at adasystems.uk and associated applications.</p>
<p><strong>"Service"</strong> means the Software as a Service (SaaS) fleet management solution provided by ADASystems, including the Platform and Device functionality.</p>
<p><strong>"Customer"</strong> or "Superuser" means the business entity or individual who purchases the Service and Device(s).</p>
<p><strong>"User"</strong> means any individual authorised by the Customer to access the Platform, including drivers and fleet managers.</p>

<h2>2. Device Ownership and Administration Rights</h2>
<h3>2.1 Device Ownership</h3>
<p>Upon purchase and full payment, the Customer acquires physical ownership of the Device hardware. However, the Customer acknowledges and agrees that:</p>
<ul>
    <li>The embedded software, firmware, and operating system remain the exclusive intellectual property of ADASystems.</li>
    <li>The Device is designed to operate exclusively with the ADASystems Platform and Service.</li>
    <li>ADASystems retains full administrative control and remote management capabilities over all Devices.</li>
</ul>

<h3>2.2 Administrative Control</h3>
<div class="highlight-box">
    <p><strong>IMPORTANT:</strong> ADASystems maintains permanent administrative rights over all Devices, regardless of physical ownership. This includes remote configuration, updates, diagnostics, monitoring, security patches, and remote deactivation in cases of service termination, non-payment, or breach of these Terms.</p>
</div>

<h3>2.3 Restrictions</h3>
<p>The Customer shall NOT:</p>
<ul>
    <li>Attempt to modify, reverse engineer, decompile, or disassemble the Device software or firmware</li>
    <li>Remove, disable, or circumvent any administrative or security features</li>
    <li>Use the Device with any third-party software or platform not authorised by ADASystems</li>
    <li>Resell, lease, or transfer the Device without prior written consent from ADASystems</li>
    <li>Tamper with or physically modify the Device in any way that affects its functionality</li>
</ul>

<h2>3. Service Subscription and Licensing</h2>
<h3>3.1 SaaS Licence</h3>
<p>ADASystems grants the Customer a non-exclusive, non-transferable, revocable licence to access and use the Platform for the duration of the active subscription period. This licence does not constitute a sale of software.</p>

<h3>3.2 Subscription Terms</h3>
<p>Subscriptions are billed on a monthly or annual basis as agreed at the time of purchase. The Service remains active only while subscription payments are current. ADASystems reserves the right to suspend or terminate Service access for non-payment.</p>

<h3>3.3 User Hierarchy</h3>
<p>The Platform operates with a hierarchical access structure. Customers (Superusers) may create and manage User accounts for their drivers and staff. ADASystems retains super-administrative access above all customer accounts for system management and support purposes.</p>

<h2>4. Data Collection and Privacy</h2>
<p>The Device and Platform collect and process data including: vehicle location (GPS), speed, engine diagnostics (OBD-II), driver behaviour metrics, tachograph data, cellular connectivity status, and device health information.</p>
<p>ADASystems processes data in accordance with the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018. Please refer to our <a href="{{ route('legal.privacy') }}">Privacy Policy</a> for full details.</p>

<h2>5. Payment Terms</h2>
<p>Payment for Devices is due upon order unless otherwise agreed. Subscription fees are due in advance. Late payments may incur interest at 8% above the Bank of England base rate. ADASystems reserves the right to suspend Services and remotely disable Devices for accounts more than 30 days overdue.</p>

<h2>6. Warranties and Disclaimers</h2>
<h3>6.1 Hardware Warranty</h3>
<p>Devices are warranted against manufacturing defects for a period of 12 months from the date of delivery. This warranty does not cover damage caused by misuse, unauthorised modifications, or environmental factors.</p>

<h3>6.2 Service Availability</h3>
<p>ADASystems endeavours to maintain Platform availability of 99.5% but does not guarantee uninterrupted service. Please refer to our <a href="{{ route('legal.sla') }}">Service Level Agreement</a> for details.</p>

<h3>6.3 Limitation of Liability</h3>
<p>To the maximum extent permitted by law, ADASystems shall not be liable for any indirect, incidental, special, or consequential damages arising from the use of the Service or Device.</p>

<h2>7. Termination</h2>
<h3>7.1 By Customer</h3>
<p>Customers may terminate their subscription by providing 30 days written notice. No refunds will be provided for unused subscription periods.</p>

<h3>7.2 By ADASystems</h3>
<p>ADASystems may terminate or suspend Service immediately for breach of these Terms, non-payment, or illegal use. Upon termination, administrative control of Devices remains with ADASystems and Devices may be remotely deactivated.</p>

<h3>7.3 Effect of Termination</h3>
<p>Upon termination, the Customer loses access to the Platform and Device functionality. The Device hardware remains physically with the Customer but will be non-functional without an active Service subscription.</p>

<h2>8. Intellectual Property</h2>
<p>All intellectual property rights in the Platform, Device software, firmware, and associated documentation remain the exclusive property of ADASystems. No rights are transferred to the Customer except the limited licence to use the Service as described herein.</p>

<h2>9. Compliance and Regulatory</h2>
<p>Customers are responsible for ensuring their use of the Service complies with all applicable laws, including EU Tachograph Regulations, road transport regulations, and employment laws regarding driver monitoring.</p>

<h2>10. Governing Law</h2>
<p>These Terms and Conditions shall be governed by and construed in accordance with the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p>

<h2>11. Changes to Terms</h2>
<p>ADASystems reserves the right to modify these Terms at any time. Customers will be notified of material changes via email or Platform notification. Continued use of the Service following notification constitutes acceptance of the revised Terms.</p>

<h2>12. Contact Information</h2>
<p>For questions regarding these Terms and Conditions, please contact:</p>
<p><strong>ADASystems</strong><br>Birmingham, United Kingdom<br>Email: <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a><br>Website: adasystems.uk</p>

<div class="highlight-box">
    <p>By purchasing a Device or subscribing to the ADASystems Platform, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
</div>
@endsection
