<?php
session_start();
error_reporting(0);

// Check if logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: index.php");
    exit();
}

include_once('../include/db_config.php');
include_once('../lib/Agent.class.php');

$agent = new Agent();
$agentId = $_SESSION['agent_id'];
$agentData = $agent->getAgentById($agentId);

// Get all transactions
$transactions = $agent->getTransactions($agentId, 100);

include_once('include_head.php');
include_once('include_nav.php');
?>

<style>
.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-topup {
    background: #d1fae5;
    color: #065f46;
}

.badge-generate {
    background: #fee2e2;
    color: #991b1b;
}

.badge-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
}

.status-success {
    background: #dcfce7;
    color: #166534;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-failed {
    background: #fee2e2;
    color: #b91c1c;
}

.status-note {
    margin-top: 4px;
    font-size: 11px;
    color: #475569;
}

.serial-chip {
    display: inline-block;
    margin-top: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    background: rgba(15,23,42,0.08);
    font-family: 'Courier New', monospace;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.4px;
}

.dark .serial-chip {
    background: rgba(255,255,255,0.08);
    color: #f9fafb;
}

.desktop-only {
    display: block;
}

.mobile-only {
    display: none;
}

.transaction-card {
    background: #fff;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(15,23,42,0.08);
    border: 1px solid rgba(15,23,42,0.06);
}

.transaction-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.transaction-card .card-header .date {
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
}

.transaction-card .card-header .type-badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #fff;
    background: #475569;
}

.transaction-card .badge-topup {
    background: #10b981;
}

.transaction-card .badge-generate,
.transaction-card .badge-digiflazz {
    background: #2563eb;
}

.transaction-card .card-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 12px;
}

.transaction-card .label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #475569;
}

.transaction-card .value {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    word-break: break-word;
}

.transaction-card .value-muted {
    color: #94a3b8;
    font-weight: 500;
}

.transaction-card .value-amount {
    font-size: 16px;
    font-weight: 700;
}

.transaction-card .value-amount.positive {
    color: #10b981;
}

.transaction-card .value-amount.negative {
    color: #ef4444;
}

.transaction-card .status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.transaction-card .status-note {
    font-size: 12px;
    color: #475569;
    margin-top: 6px;
}

.transaction-card .print-btn {
    margin-top: 16px;
    width: 100%;
    justify-content: center;
    font-weight: 700;
    border-radius: 10px;
    padding: 10px 16px;
}

.dark .transaction-card {
    background: #111827;
    border-color: #1f2937;
    box-shadow: 0 12px 28px rgba(15,23,42,0.4);
}

.dark .transaction-card .label {
    color: #cbd5f5;
}

.dark .transaction-card .value {
    color: #f1f5f9;
}

.dark .transaction-card .value-muted {
    color: #94a3b8;
}

.payment-receipt-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 4000;
}

.payment-receipt-modal {
    background: #ffffff;
    border-radius: 18px;
    padding: 26px;
    max-width: 440px;
    width: 100%;
    position: relative;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25);
    color: #0f172a;
    font-size: 15px;
    line-height: 1.6;
}

.payment-receipt-modal h4 {
    font-weight: 700;
    margin-bottom: 8px;
    font-size: 22px;
    color: #0b1f33;
}

.payment-receipt-modal p.subtitle {
    margin: 0 0 18px;
    color: #1e293b;
    font-size: 15px;
    font-weight: 500;
}

.receipt-close {
    position: absolute;
    top: 14px;
    right: 16px;
    border: none;
    background: none;
    font-size: 22px;
    color: #64748b;
    cursor: pointer;
}

.payment-receipt-summary dl {
    display: grid;
    grid-template-columns: 140px 1fr;
    row-gap: 8px;
    column-gap: 12px;
    margin: 0;
}

.payment-receipt-summary dt {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #1e293b;
    font-weight: 700;
}

.payment-receipt-summary dd {
    margin: 0;
    font-weight: 700;
    font-size: 17px;
    color: #0b1f33;
    letter-spacing: 0.2px;
}

.payment-receipt-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 22px;
}

.payment-receipt-actions button {
    flex: 1 1 calc(50% - 10px);
    border: none;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.payment-receipt-actions button i {
    margin-right: 6px;
    font-size: 15px;
}

#printNormalBtn {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
}

#printThermalBtn {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #fff;
}

#receiptDoneBtn {
    background: #f1f5f9;
    color: #1e293b;
    border: 1px solid #cbd5f5;
}

@media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }

    .mobile-only {
        display: block;
    }

    .transaction-card {
        display: block;
        width: 100%;
    }

    .table-responsive {
        overflow-x: hidden !important;
        width: 100% !important;
    }

    .transactions-table {
        display: none !important;
    }

    .transactions-table thead,
    .transactions-table tbody,
    .transactions-table tr,
    .transactions-table td {
        display: none !important;
    }
}

/* Mobile responsive table */
@media (max-width: 768px) {
    .content-wrapper {
        padding-left: 10px !important;
        padding-right: 10px !important;
        overflow-x: visible !important;
    }
    
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    .card {
        margin-bottom: 10px !important;
        border-radius: 4px !important;
    }
    
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
        max-width: 100% !important;
        display: block !important;
        margin: 0 !important;
        -ms-overflow-style: -ms-autohiding-scrollbar !important;
        position: relative !important;
    }
    
    .table-responsive::-webkit-scrollbar {
        height: 8px !important;
        -webkit-appearance: none !important;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
        border-radius: 4px !important;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #888 !important;
        border-radius: 4px !important;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555 !important;
    }
    
    .table-responsive table {
        width: 100% !important;
        min-width: 600px !important;
        font-size: 12px !important;
        margin-bottom: 0 !important;
        display: table !important;
        table-layout: auto !important;
    }
    
    .table-responsive th,
    .table-responsive td {
        padding: 8px 6px !important;
        white-space: nowrap !important;
        font-size: 11px !important;
    }
    
    .card-body {
        padding: 10px !important;
        overflow-x: visible !important;
        overflow-y: visible !important;
        max-width: 100% !important;
    }
    
    .card {
        overflow: visible !important;
        max-width: 100% !important;
    }
    
    .card-header {
        padding: 10px !important;
        font-size: 14px !important;
    }
    
    .card-header h3 {
        font-size: 16px !important;
        margin-bottom: 5px !important;
    }
    
    /* Make table more compact on mobile */
    .table-bordered {
        border-collapse: collapse !important;
    }
    
    .badge {
        padding: 3px 8px !important;
        font-size: 10px !important;
    }
}

/* Add styles for the editable price field */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #475569;
    font-size: 13px;
    text-transform: uppercase;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #0f172a; /* Darker text for better visibility */
    background-color: #ffffff;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dark .form-control {
    background-color: #1e293b;
    border-color: #334155;
    color: #f1f5f9; /* Lighter text for better visibility in dark mode */
}
