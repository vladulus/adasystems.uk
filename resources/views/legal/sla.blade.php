@extends('legal.layout')

@section('title', 'Service Level Agreement (SLA) - ADASystems Fleet Management')
@section('description', 'ADASystems Service Level Agreement defines uptime commitments, support response times, and service guarantees for our fleet management platform.')
@section('keywords', 'ADASystems SLA, service level agreement, fleet management uptime, platform availability, support response time')
@section('page_title', 'Service Level Agreement')
@section('page_subtitle', 'Our Commitment to Service Quality')

@section('legal_content')
<h2>1. Overview</h2>
<p>This Service Level Agreement ("SLA") defines the service commitments provided by ADASystems to Customers using our fleet management platform and Devices. This SLA is incorporated into and subject to our Terms and Conditions.</p>

<h2>2. Platform Availability</h2>
<h3>2.1 Uptime Commitment</h3>
<p>ADASystems commits to a monthly platform availability of <strong>99.5%</strong> ("Uptime Target"). Availability is calculated as: ((Total Minutes - Downtime Minutes) / Total Minutes) × 100</p>

<h3>2.2 Uptime Calculation</h3>
<p>Monthly uptime percentages correspond to the following maximum downtime:</p>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Uptime %</th>
            <th>Max Monthly Downtime</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>99.9%</td>
            <td>43 minutes</td>
        </tr>
        <tr>
            <td><strong>99.5%</strong></td>
            <td><strong>3 hours 36 minutes</strong></td>
        </tr>
        <tr>
            <td>99.0%</td>
            <td>7 hours 18 minutes</td>
        </tr>
    </tbody>
</table>
</div>

<h3>2.3 Exclusions</h3>
<p>The following are excluded from uptime calculations:</p>
<ul>
    <li>Scheduled maintenance (notified at least 24 hours in advance)</li>
    <li>Emergency security patches</li>
    <li>Issues caused by Customer actions or third-party services</li>
    <li>Force majeure events</li>
    <li>Internet connectivity issues outside our infrastructure</li>
</ul>

<h2>3. Device Performance</h2>
<h3>3.1 Data Transmission</h3>
<p>Devices will transmit telemetry data to the platform within 60 seconds of generation under normal cellular network conditions. Data is buffered locally during connectivity outages and transmitted upon reconnection.</p>

<h3>3.2 GPS Accuracy</h3>
<p>GPS location accuracy is typically within 5 metres under open sky conditions. Accuracy may be reduced in urban canyons, tunnels, or areas with poor satellite visibility.</p>

<h2>4. Support Services</h2>
<h3>4.1 Support Hours</h3>
<p>Standard support is available Monday to Friday, 9:00 AM to 5:00 PM GMT, excluding UK public holidays. Emergency support for critical issues is available 24/7.</p>

<h3>4.2 Response Times</h3>
<p>We commit to the following initial response times based on issue severity:</p>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Severity</th>
            <th>Description</th>
            <th>Response Time</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong style="color: #c53030;">Critical</strong></td>
            <td>Platform down, all tracking unavailable</td>
            <td><strong>1 hour</strong></td>
        </tr>
        <tr>
            <td><strong style="color: #dd6b20;">High</strong></td>
            <td>Major feature unavailable, multiple users affected</td>
            <td><strong>4 hours</strong></td>
        </tr>
        <tr>
            <td><strong style="color: #d69e2e;">Medium</strong></td>
            <td>Feature degraded, workaround available</td>
            <td><strong>8 hours</strong></td>
        </tr>
        <tr>
            <td><strong style="color: #38a169;">Low</strong></td>
            <td>Minor issue, cosmetic, feature request</td>
            <td><strong>24 hours</strong></td>
        </tr>
    </tbody>
</table>
</div>

<h2>5. Scheduled Maintenance</h2>
<p>Scheduled maintenance windows are typically Sunday 02:00-06:00 GMT. Customers will be notified at least 24 hours in advance via email and platform notification. Emergency maintenance may occur without notice when required for security or stability.</p>

<h2>6. Service Credits</h2>
<p>If ADASystems fails to meet the 99.5% uptime commitment, eligible Customers may request service credits. Credit amounts are calculated as a percentage of the monthly subscription fee based on actual uptime achieved. Service credits are the sole remedy for SLA breaches and must be requested within 30 days.</p>

<h2>7. Reporting</h2>
<p>Customers can view real-time platform status and historical uptime reports via the dashboard. Incident reports are provided upon request for any significant service disruptions.</p>

<h2>8. Contact</h2>
<p>For support enquiries:</p>
<p><strong>ADASystems</strong><br>Birmingham, United Kingdom<br>Email: <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a></p>
@endsection
