<?php
/*
 * Billing Payment Callback - Handle payment gateway callbacks
 */

// Set content type
header('Content-Type: application/json');

try {
    // Get raw POST data
    $input = file_get_contents('php://input');
    
    // Log the callback data for debugging
    error_log("Billing Payment Callback - Raw data: " . $input);
    
    // Parse JSON data
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }
    
    // Log parsed data
    error_log("Billing Payment Callback - Parsed data: " . json_encode($data));
    
    // Include required files
    require_once(__DIR__ . '/../include/db_config.php');
    require_once(__DIR__ . '/../lib/BillingService.class.php');
    
    // Get database connection
    $db = getDBConnection();
    
    // Extract reference number from callback data
    $reference = $data['reference'] ?? $data['reference_number'] ?? $data['merchant_ref'] ?? null;
    
    if (!$reference) {
        throw new Exception('Reference number not found in callback data');
    }
    
    // Find the invoice by reference
    $stmt = $db->prepare("SELECT * FROM billing_invoices WHERE reference_number = :reference LIMIT 1");
    $stmt->execute([':reference' => $reference]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$invoice) {
        throw new Exception('Invoice not found for reference: ' . $reference);
    }
    
    // Check if invoice is already paid
    if ($invoice['status'] === 'paid') {
        echo json_encode(['success' => true, 'message' => 'Invoice already paid']);
        exit;
    }
    
    // Get payment status from callback data
    $paymentStatus = 'pending';
    
    // Determine payment status based on different gateway formats
    if (isset($data['status'])) {
        $paymentStatus = strtolower($data['status']);
    } elseif (isset($data['transaction_status'])) {
        $paymentStatus = strtolower($data['transaction_status']);
    } elseif (isset($data['payment_status'])) {
        $paymentStatus = strtolower($data['payment_status']);
    }
    
    // Map status to our system
    $mappedStatus = 'pending';
    switch ($paymentStatus) {
        case 'paid':
        case 'success':
        case 'settlement':
        case 'capture':
            $mappedStatus = 'paid';
            break;
        case 'expired':
        case 'cancel':
        case 'deny':
            $mappedStatus = 'failed';
            break;
        default:
            $mappedStatus = 'pending';
    }
    
    // If payment is successful, update invoice
    if ($mappedStatus === 'paid') {
        $billingService = new BillingService();
        
        // Mark invoice as paid
        $result = $billingService->markInvoicePaid((int)$invoice['id'], [
            'payment_channel' => $data['payment_method'] ?? $data['payment_channel'] ?? 'online_payment',
            'reference_number' => $reference,
            'paid_via' => 'online_payment',
            'paid_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Record payment
            $billingService->recordPayment((int)$invoice['id'], (float)$invoice['amount'], [
                'method' => $data['payment_method'] ?? $data['payment_channel'] ?? 'online_payment',
                'notes' => 'Payment via online gateway'
            ]);
            
            // Restore customer profile if isolated
            $customerStmt = $db->prepare("SELECT * FROM billing_customers WHERE id = :id LIMIT 1");
            $customerStmt->execute([':id' => $invoice['customer_id']]);
            $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($customer && (int)$customer['is_isolated'] === 1) {
                $billingService->restoreCustomerProfile((int)$customer['id']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Payment processed successfully']);
            exit;
        } else {
            throw new Exception('Failed to update invoice status');
        }
    } elseif ($mappedStatus === 'failed') {
        // Handle failed payment if needed
        echo json_encode(['success' => true, 'message' => 'Payment failed recorded']);
        exit;
    } else {
        // Payment is still pending
        echo json_encode(['success' => true, 'message' => 'Payment status is pending']);
        exit;
    }
    
} catch (Exception $e) {
    error_log("Billing Payment Callback Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
} catch (Throwable $e) {
    error_log("Billing Payment Callback Throwable: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
    exit;
}