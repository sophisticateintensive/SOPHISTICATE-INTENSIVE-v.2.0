<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sophisticate Intensive Portal Version 2.0 - Upgrade Agreement & Invoice</title>
    <style>
        @page {
            margin: 16mm 16mm 16mm 16mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2.5px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-title {
            font-size: 16pt;
            font-weight: 800;
            color: #1e3a8a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 10pt;
            color: #475569;
            font-weight: 600;
            margin: 0;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .meta-table .label {
            width: 28%;
            font-weight: bold;
            color: #475569;
        }
        .meta-table .val {
            width: 72%;
            color: #0f172a;
        }
        h2 {
            font-size: 11pt;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        p {
            margin: 0 0 6px 0;
            font-size: 9.5pt;
            text-align: justify;
        }
        ul {
            margin: 0 0 8px 0;
            padding-left: 20px;
            font-size: 9.5pt;
        }
        li {
            margin-bottom: 3px;
        }
        .feature-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
        }
        .feature-grid th, .feature-grid td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .feature-grid th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .feature-grid tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .tag-v2 {
            color: #059669;
            font-weight: bold;
        }
        table.pricing {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px 0;
        }
        table.pricing th, table.pricing td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 9.5pt;
            text-align: left;
        }
        table.pricing th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        table.pricing tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .total-row {
            font-weight: bold;
            background-color: #eff6ff !important;
            color: #1e3a8a;
            font-size: 10pt;
        }
        .highlight-box {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 9.5pt;
        }
        .signatures {
            margin-top: 18px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            padding: 5px 15px 0 0;
            vertical-align: top;
            font-size: 9pt;
        }
        .sig-line {
            border-bottom: 1px solid #0f172a;
            height: 35px;
            margin-bottom: 4px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">Sophisticate Intensive Classes</div>
        <div class="header-sub">System Handover, Feature Breakdown & Upgrade Invoice (Version 2.0)</div>
        <span class="badge">Major System Release · Production Ready</span>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Date:</td>
                <td class="val">{{ date }}</td>
            </tr>
            <tr>
                <td class="label">Client Institution:</td>
                <td class="val">Sophisticate Intensive Classes — School Administration</td>
            </tr>
            <tr>
                <td class="label">Project / System:</td>
                <td class="val">School Management & Student Portal System (Upgrade to Version 2.0)</td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td class="val"><strong>Completed, Fully Tested & Deployed</strong></td>
            </tr>
        </table>
    </div>

    <h2>1. Executive Summary & Upgrade Overview</h2>
    <p>
        This document serves as the formal handover breakdown and billing agreement for the delivery of the <strong>Sophisticate Intensive Classes Portal Version 2.0</strong>. Version 2.0 is a major system overhaul specifically engineered to automate class timetabling, protect tuition revenue through fee-gated resources, introduce online examinations, and secure all school records with off-site cloud backups.
    </p>

    <h2>2. Complete Breakdown of What Was Built & Delivered</h2>

    <table class="feature-grid">
        <thead>
            <tr>
                <th style="width: 25%;">Feature Module</th>
                <th style="width: 42%;">What Was Built & Delivered</th>
                <th style="width: 33%;">Business Value to School</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Supabase Cloud Backup & Storage</strong></td>
                <td>Automated daily off-site cloud sync of the complete database (students, fees, grades, timetables) plus dedicated cloud storage for all study files.</td>
                <td><span class="tag-v2">Zero Data Loss:</span> Protects school from hardware failure or laptop theft. Complete instant recovery.</td>
            </tr>
            <tr>
                <td><strong>2. Fee-Gated Resource Protection</strong></td>
                <td>Server-enforced access locks: Students who have not paid fees cannot view resources; under 50% paid can view in a protected reader; 50%+ paid can download.</td>
                <td><span class="tag-v2">Revenue Protection:</span> Forces students to clear outstanding school fees before getting access to lesson notes and past papers.</td>
            </tr>
            <tr>
                <td><strong>3. Dynamic Weekly Timetables</strong></td>
                <td>Date-specific weekly schedule manager with classroom conflict detection, week navigation, student view, and <strong>1-Click Week Duplication</strong>.</td>
                <td><span class="tag-v2">Time Savings:</span> Eliminates paper schedule chaos and saves administrative time each week.</td>
            </tr>
            <tr>
                <td><strong>4. Interactive Quiz & Testing Engine</strong></td>
                <td>Online testing engine with multiple question types (MCQ, True/False, Short Answer), randomized pools, timed tests, instant auto-grading, and answer review controls.</td>
                <td><span class="tag-v2">Exam Automation:</span> Saves teachers dozens of marking hours and gives students instant grade analysis.</td>
            </tr>
            <tr>
                <td><strong>5. Enterprise Security Hardening</strong></td>
                <td>Removed public self-registration (admin-controlled only), 1-click student deactivation switch, HTTP anti-tamper security headers, and brute-force login limits.</td>
                <td><span class="tag-v2">Unhackable Integrity:</span> Strict role separation and absolute privacy for student grades and financial records.</td>
            </tr>
            <tr>
                <td><strong>6. Desktop & Mobile PWA App</strong></td>
                <td>Installs directly on Windows PC, Mac, Android, and iPhone with offline shell caching and automatic version update detection.</td>
                <td><span class="tag-v2">Modern App Experience:</span> Works fast on any smartphone, tablet, or desktop computer.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>3. Financial Invoice & Payment Breakdown</h2>
    <p>
        The commercial fee for the engineering, security hardening, cloud integration, data migration, and deployment of Version 2.0 is structured as follows:
    </p>

    <table class="pricing">
        <thead>
            <tr>
                <th>Item Description</th>
                <th>Payment Type</th>
                <th>Amount (MWK)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Version 2.0 System Upgrade & Cloud Infrastructure</strong>
                    <br>
                    <small style="color:#64748b;">
                        Full delivery of Weekly Timetable Module, Fee-Gated Resource Engine, Quiz Platform, Supabase Cloud Database & Storage, Security Overhaul, and PWA Application.
                    </small>
                </td>
                <td>One-Off Upgrade Fee</td>
                <td><strong>MK 300,000</strong></td>
            </tr>
            <tr>
                <td>
                    <strong>Monthly Cloud Maintenance & Technical Support Retainer</strong>
                    <br>
                    <small style="color:#64748b;">
                        Daily automated cloud backups, continuous database optimization, error monitoring, and priority technical support.
                    </small>
                </td>
                <td>Monthly Retainer (Optional Ongoing)</td>
                <td><strong>MK 70,000 / month</strong></td>
            </tr>
            <tr class="total-row">
                <td colspan="2">TOTAL UPGRADE AMOUNT DUE (UPON DELIVERY):</td>
                <td><strong>MK 300,000</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="highlight-box">
        <strong>Payment Terms:</strong> The upgrade fee of <strong>MK 300,000</strong> is payable upon receipt of this document and handover of Version 2.0. Bank transfer or Airtel Money / Mpamba mobile payment details will be provided.
    </div>

    <h2>4. Scope of Ongoing Monthly Maintenance (MK 70,000 / Month)</h2>
    <p>The ongoing maintenance retainer includes:</p>
    <ul>
        <li><strong>Automated Cloud Backups:</strong> Nightly cloud sync monitoring to guarantee data safety.</li>
        <li><strong>Error & Bug Resolution:</strong> Immediate technical troubleshooting for any reported issues.</li>
        <li><strong>Administrative Assistance:</strong> Support with academic year rollover, term setup, and bulk enrollments.</li>
    </ul>

    <h2>5. Acceptance & Handover Authorization</h2>
    <p>
        By signing below, the School Administration confirms receipt of the Version 2.0 System Upgrade and approves the billing schedule above.
    </p>

    <table class="signatures">
        <tr>
            <td>
                <strong>FOR THE CLIENT (School Administration):</strong>
                <div class="sig-line"></div>
                <p>Representative Name: ____________________________</p>
                <p>Designation: ____________________________________</p>
                <p>Signature: ___________________ &nbsp; Date: ___________</p>
            </td>
            <td>
                <strong>FOR THE LEAD DEVELOPER:</strong>
                <div class="sig-line"></div>
                <p>Developer Name: _________________________________</p>
                <p>Designation: Lead Software Engineer</p>
                <p>Signature: ___________________ &nbsp; Date: ___________</p>
            </td>
        </tr>
    </table>

</body>
</html>