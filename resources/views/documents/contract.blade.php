<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Software Upgrade & Maintenance Agreement</title>
    <style>
        @page {
            margin: 25mm 20mm 25mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            font-size: 11pt;
            color: #64748b;
            margin: 0;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 10pt;
            vertical-align: top;
        }
        .meta-table .label {
            width: 25%;
            font-weight: bold;
            color: #475569;
        }
        .meta-table .val {
            width: 75%;
            color: #0f172a;
        }
        h2 {
            font-size: 12pt;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        p {
            margin: 0 0 8px 0;
            font-size: 10pt;
            text-align: justify;
        }
        ul {
            margin: 0 0 10px 0;
            padding-left: 20px;
            font-size: 10pt;
        }
        li {
            margin-bottom: 4px;
        }
        table.pricing {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px 0;
        }
        table.pricing th, table.pricing td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 10pt;
            text-align: left;
        }
        table.pricing th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            letter-spacing: 0.5px;
        }
        table.pricing tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .total-row {
            font-weight: bold;
            background-color: #eff6ff !important;
            color: #1e3a8a;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            padding: 10px 20px 0 0;
            vertical-align: top;
            font-size: 10pt;
        }
        .sig-line {
            border-bottom: 1px solid #0f172a;
            height: 45px;
            margin-bottom: 6px;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Software Upgrade & Maintenance Agreement</h1>
        <p class="subtitle">System Version 2.0 Deployment & Ongoing Service Level Agreement (SLA)</p>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Date of Agreement:</td>
                <td class="val">{{ date('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Client / Institution:</td>
                <td class="val">Sophisticate Intensive Classes / School Administration</td>
            </tr>
            <tr>
                <td class="label">Service Provider:</td>
                <td class="val">Lead Software Engineer & Systems Architect</td>
            </tr>
            <tr>
                <td class="label">Project / System:</td>
                <td class="val">School Management & Student Portal System (Upgrade to Version 2.0)</td>
            </tr>
        </table>
    </div>

    <h2>1. Purpose of Agreement</h2>
    <p>
        This Agreement governs the provision, deployment, and ongoing technical maintenance of the <strong>Version 2.0 System Upgrade</strong> for the Client's School Management & Student Portal System. This agreement replaces all prior informal understandings regarding Version 1.0 maintenance.
    </p>

    <h2>2. Scope of Version 2.0 Upgrade Deliverables</h2>
    <p>The Service Provider has engineered and delivered the following major system capabilities:</p>
    <ul>
        <li><strong>Fee-Gated Resource Protection Engine:</strong> Strict server-side access control restricting viewing and downloading of uploaded academic resources based on student fee settlement thresholds (under 50% vs. 50%+ paid).</li>
        <li><strong>In-Portal Secure Document Reader:</strong> Integrated canvas reader preventing direct external link leakage and browser toolbar downloads for restricted accounts.</li>
        <li><strong>Dynamic Weekly Timetable Management:</strong> Transitioned schedule engine from static semester timetables to date-specific weekly schedules with a 1-click week duplication tool.</li>
        <li><strong>System Security & Hardening Overhaul:</strong> Implementation of HTTP security headers (Anti-Clickjacking, MIME-Sniffing prevention), upload filetype whitelisting, and brute-force login throttling.</li>
        <li><strong>Database Migration & Data Preservation:</strong> Seamless migration of all existing student, enrollment, and grade records to the Version 2.0 schema without data loss.</li>
    </ul>

    <h2>3. Financial Terms & Payment Schedule</h2>
    <p>The Client agrees to pay the Service Provider according to the following schedule:</p>

    <table class="pricing">
        <thead>
            <tr>
                <th>Service Item</th>
                <th>Payment Type</th>
                <th>Amount (MWK)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Version 2.0 Upgrade, Migration & Deployment</strong><br><small style="color:#64748b;">Full codebase upgrade, security hardening & server installation</small></td>
                <td>One-Off Payment (Upon Delivery)</td>
                <td><strong>MK 350,000</strong></td>
            </tr>
            <tr>
                <td><strong>Monthly System Maintenance & Support Retainer</strong><br><small style="color:#64748b;">Cloud backups, uptime monitoring, bug fixes & technical support</small></td>
                <td>Recurring Monthly Fee</td>
                <td><strong>MK 70,000 / month</strong></td>
            </tr>
            <tr class="total-row">
                <td colspan="2">Initial Deployment Amount Payable:</td>
                <td><strong>MK 350,000</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>4. Scope of Monthly Maintenance Services (MK 70,000 / Month)</h2>
    <p>The ongoing monthly retainer covers the following operational services:</p>
    <ul>
        <li><strong>Cloud Database Backups:</strong> Routine database snapshots and cloud synchronization monitoring to ensure zero data loss.</li>
        <li><strong>System Health & Security Monitoring:</strong> Continuous monitoring of server response, storage integrity, and error logs.</li>
        <li><strong>Corrective Bug Fixes:</strong> Immediate investigation and resolution of any unexpected system errors or bugs reported by staff or students.</li>
        <li><strong>Administrative Technical Support:</strong> Direct priority technical support (via Phone, WhatsApp, or Email) during normal administrative working hours.</li>
    </ul>
    <p><em>Exclusions:</em> Building brand-new, unrelated functional modules (e.g., separate payroll or inventory modules) will be quoted separately as distinct projects.</p>

    <h2>5. Data Security & Confidentiality</h2>
    <p>
        The Service Provider agrees to maintain strict confidentiality regarding all student records, examination results, fee collection figures, and institutional data. No client data will ever be shared, sold, or exposed to third parties.
    </p>

    <h2>6. Term & Termination</h2>
    <p>
        The monthly maintenance retainer shall commence upon deployment and continue on a month-to-month basis. Either party may terminate the monthly maintenance retainer by providing thirty (30) days written notice.
    </p>

    <h2>7. Acceptance & Authorization</h2>
    <p>
        By signing below, both parties acknowledge and accept the deliverables, terms, and payment schedules outlined in this Agreement.
    </p>

    <table class="signatures">
        <tr>
            <td>
                <strong>FOR THE CLIENT (School Administration):</strong>
                <div class="sig-line"></div>
                <p>Authorized Representative Name: _______________________</p>
                <p>Title / Designation: ________________________________</p>
                <p>Signature: __________________ &nbsp; Date: ____________</p>
            </td>
            <td>
                <strong>FOR THE SERVICE PROVIDER (Developer):</strong>
                <div class="sig-line"></div>
                <p>Developer Name: _________________________________</p>
                <p>Title: Lead Software Engineer</p>
                <p>Signature: __________________ &nbsp; Date: ____________</p>
            </td>
        </tr>
    </table>

</body>
</html>
