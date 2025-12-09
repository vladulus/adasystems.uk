@extends('legal.layout')

@section('title', 'Cookie Policy - ADASystems Fleet Management Platform')
@section('description', 'Learn about the cookies used on ADASystems fleet management platform. Understand how we use essential, functional, and analytics cookies to improve your experience.')
@section('keywords', 'ADASystems cookies, cookie policy, fleet management cookies, website cookies UK, GDPR cookies')
@section('page_title', 'Cookie Policy')
@section('page_subtitle', 'How We Use Cookies on Our Platform')

@section('legal_content')
<h2>1. What Are Cookies?</h2>
<p>Cookies are small text files placed on your device when you visit our website. They help us provide you with a better experience by remembering your preferences, keeping you logged in, and understanding how you use our platform.</p>

<h2>2. Types of Cookies We Use</h2>
<h3>2.1 Essential Cookies</h3>
<p>These cookies are necessary for the website to function and cannot be disabled. They include session cookies for authentication and security cookies to protect your account.</p>

<h3>2.2 Functional Cookies</h3>
<p>These cookies remember your preferences such as language settings, dashboard layout, and display options to provide a personalised experience.</p>

<h3>2.3 Analytics Cookies</h3>
<p>We use analytics cookies to understand how visitors interact with our website. This helps us improve our services. These cookies collect anonymous statistical data.</p>

<h3>2.4 Marketing Cookies</h3>
<p>These cookies may be set by our advertising partners to build a profile of your interests and show you relevant adverts on other sites. We only use these with your consent.</p>

<h2>3. Specific Cookies Used</h2>
<p>The following table lists the main cookies used on our platform:</p>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Cookie Name</th>
            <th>Type</th>
            <th>Duration</th>
            <th>Purpose</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>session_token</td>
            <td>Essential</td>
            <td>Session</td>
            <td>User authentication</td>
        </tr>
        <tr>
            <td>XSRF-TOKEN</td>
            <td>Essential</td>
            <td>Session</td>
            <td>Security / CSRF protection</td>
        </tr>
        <tr>
            <td>remember_token</td>
            <td>Functional</td>
            <td>30 days</td>
            <td>Remember me functionality</td>
        </tr>
        <tr>
            <td>preferences</td>
            <td>Functional</td>
            <td>1 year</td>
            <td>User preferences storage</td>
        </tr>
        <tr>
            <td>_ga, _gid</td>
            <td>Analytics</td>
            <td>2 years / 24h</td>
            <td>Google Analytics tracking</td>
        </tr>
        <tr>
            <td>cookie_consent</td>
            <td>Essential</td>
            <td>1 year</td>
            <td>Stores cookie preferences</td>
        </tr>
    </tbody>
</table>
</div>

<h2>4. Managing Cookies</h2>
<p>You can control and manage cookies through your browser settings. Most browsers allow you to refuse or accept cookies, delete existing cookies, and set preferences for certain websites.</p>
<p>Please note that disabling essential cookies may affect the functionality of our platform and you may not be able to access certain features.</p>
<p>To manage your cookie preferences on our platform, you can use the cookie settings panel accessible from the footer of our website.</p>

<h2>5. Third-Party Cookies</h2>
<p>Some cookies are placed by third-party services that appear on our pages. We do not control these cookies. Please refer to the respective third-party privacy policies for more information.</p>

<h2>6. Updates to This Policy</h2>
<p>We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated revision date.</p>

<h2>7. Contact Us</h2>
<p>If you have questions about our use of cookies:</p>
<p><strong>ADASystems</strong><br>Birmingham, United Kingdom<br>Email: <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a></p>
@endsection
