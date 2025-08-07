<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="text-end mb-3">
        <div class="btn-group">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Print Payslip
            </button>
            
            <?php if(has_permission('mark_payslips_paid') && $payslip['status'] == 'generated'): ?>
            <a href="<?= base_url('payslips/admin/mark-as-paid/' . $payslip['id']) ?>" class="btn btn-success" 
            onclick="return confirm('Mark this payslip as paid?')">
                <i class="bi bi-check-circle me-2"></i> Mark as Paid
            </a>
            <?php endif; ?>
            
            <?php if(has_permission('edit_payslips') && $payslip['status'] != 'cancelled'): ?>
            <a href="<?= base_url('payslips/admin/cancel/' . $payslip['id']) ?>" class="btn btn-danger" 
            onclick="return confirm('Are you sure you want to cancel this payslip?')">
                <i class="bi bi-x-circle me-2"></i> Cancel Payslip
            </a>
            <?php endif; ?>
            
            <?php if($payslip['status'] === 'generated' && has_permission('delete_payslips')): ?>
            <a href="<?= base_url('payslips/admin/delete/' . $payslip['id']) ?>" 
               class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this payslip? This action cannot be undone.')">
                <i class="bi bi-trash me-1"></i> Delete Payslip
            </a>
            <?php endif; ?>
            
            <a href="<?= base_url('payslips/admin') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>
    
    <div class="card" id="payslip">
        <div class="card-body">
            <!-- Header -->
            <div class="text-center mb-4">
                <h3>Payslip For <?= (new \App\Models\PayslipModel())->getMonthName($payslip['month']) ?> <?= $payslip['year'] ?></h3>
                <h4><?= strtoupper($company['name']) ?></h4>
                <p class="small"><?= $company['ssm_number'] ? '(' . $company['ssm_number'] . ')' : '' ?></p>
            </div>
            
            <!-- Employee Info -->
            <div class="row mb-4">
                <div class="col-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="120">Pay Date</td>
                            <td>: <?= date('Y/m/d', strtotime($payslip['pay_date'])) ?></td>
                        </tr>
                        <tr>
                            <td>Working Days</td>
                            <td>: <?= $payslip['working_days'] ?></td>
                        </tr>
                        <tr>
                            <td width="120">Bank Name</td>
                            <td>: <?= $employee['bank_name'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td>Bank Account</td>
                            <td>: <?= $employee['bank_account'] ?? 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td style="white-space: nowrap;">Employee No</td>
                            <td style="white-space: nowrap;">: <?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Employee Name</td>
                            <td style="white-space: nowrap;">: <?= strtoupper($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Contact No.</td>
                            <td style="white-space: nowrap;">: <?= $employee['phone'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Email</td>
                            <td style="max-width: 200px;"><span style="white-space: nowrap;">: </span><span style="word-break: break-word; overflow-wrap: break-word; hyphens: auto;"><?= $employee['email'] ?? 'N/A' ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Earnings & Deductions Table -->
            <table class="table table-bordered">
                <tr style="background-color: #f0f0f0;">
                    <th width="25%">Earnings</th>
                    <th width="25%" class="text-end">Amount (<?= $payslip['currency_code'] ?? 'RM' ?>)</th>
                    <th width="25%">Deductions</th>
                    <th width="25%" class="text-end">Amount (<?= $payslip['currency_code'] ?? 'RM' ?>)</th>
                </tr>
                <tr>
                    <td>Basic Pay</td>
                    <td class="text-end"><?= number_format($payslip['basic_pay'], 2) ?></td>
                    <td>EPF Employee</td>
                    <td class="text-end"><?= number_format($payslip['epf_employee'], 2) ?></td>
                </tr>
                <tr>
                    <td>Allowance</td>
                    <td class="text-end"><?= number_format($payslip['allowance'], 2) ?></td>
                    <td>SOCSO Employee</td>
                    <td class="text-end"><?= number_format($payslip['socso_employee'], 2) ?></td>
                </tr>
                <tr>
                    <td>Overtime</td>
                    <td class="text-end"><?= number_format($payslip['overtime'], 2) ?></td>
                    <td>EIS Employee</td>
                    <td class="text-end"><?= number_format($payslip['eis_employee'], 2) ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>PCB</td>
                    <td class="text-end"><?= number_format($payslip['pcb'], 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Total Earnings</strong></td>
                    <td class="text-end"><strong><?= number_format($payslip['total_earnings'], 2) ?></strong></td>
                    <td><strong>Total Deductions</strong></td>
                    <td class="text-end"><strong><?= number_format($payslip['total_deductions'], 2) ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Net Pay</strong></td>
                    <td class="text-end"><strong><?= number_format($payslip['net_pay'], 2) ?></strong></td>
                </tr>
            </table>
            
            <!-- Additional Details -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5>Payslip Details</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Created Date:</strong> <?= date('d M Y H:i', strtotime($payslip['created_at'])) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Status:</strong> <span class="badge bg-<?= $payslip['status'] == 'paid' ? 'success' : ($payslip['status'] == 'cancelled' ? 'danger' : 'info') ?>"><?= ucfirst($payslip['status']) ?></span></p>
                                </div>
                                <div class="col-md-4">
                                    <?php if(!empty($payslip['remarks'])): ?>
                                    <p><strong>Remarks:</strong> <?= $payslip['remarks'] ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Signature Section -->
            <div class="row mt-5 pt-5">
                <div class="col-6 text-center">
                    <div class="border-top border-dark" style="width: 80%; margin: 0 auto;"></div>
                    <p class="mt-2">Employer Signature</p>
                </div>
                <div class="col-6 text-center">
                    <div class="border-top border-dark" style="width: 80%; margin: 0 auto;"></div>
                    <p class="mt-2">Employee Signature</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="small text-muted">This is a system generated payslip</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Regular styles */
    #payslip {
        font-family: Arial, sans-serif;
    }
    #payslip table {
        border-collapse: collapse;
    }
    #payslip .table-borderless td,
    #payslip .table-borderless th {
        border: none;
        padding: 3px 5px;
    }
    
    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #payslip, #payslip * {
            visibility: visible;
        }
        #payslip {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            padding: 15px;
            margin: 0;
            box-shadow: none;
            border: none;
        }
        
        /* Page settings */
        @page {
            size: A4 portrait;
            margin: 1cm;
        }
        
        /* Hide buttons */
        .btn, button {
            display: none !important;
        }
        
        /* Card styling */
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        /* Table cells should have some padding */
        td, th {
            padding: 4px 8px !important;
        }
        
        /* Make sure borderless tables stay borderless */
        .table-borderless td, 
        .table-borderless th {
            border: none !important;
        }
        
        /* Set specific widths for the main table */
        .table-bordered {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        .table-bordered td,
        .table-bordered th {
            border: 1px solid #000 !important;
        }
    }
</style>
<?= $this->endSection() ?>