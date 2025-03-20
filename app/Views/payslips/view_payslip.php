<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="text-end mb-3">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-2"></i> Print Payslip
        </button>
        <a href="<?= base_url('payslips') ?>" class="btn btn-secondary ms-2">
            <i class="bi bi-arrow-left me-2"></i> Back to List
        </a>
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
                            <td style="white-space: nowrap;">: <?= $employee['email'] ?? 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Earnings & Deductions Table -->
            <table class="table table-bordered">
                <tr style="background-color: #f0f0f0;">
                    <th width="25%">Earnings</th>
                    <th width="25%" class="text-end">Amount (RM)</th>
                    <th width="25%">Deductions</th>
                    <th width="25%" class="text-end">Amount (RM)</th>
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
            
            <!-- Amount in Words -->
            <div class="text-center my-4">
                <h5><?= number_format($payslip['net_pay'], 2) ?></h5>
                <p>
                <?php
                // Simple number to words function for the view
                function numberToWords($number) {
                    $ones = [
                        0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 
                        5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine"
                    ];
                    $teens = [
                        11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 
                        15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
                    ];
                    $tens = [
                        1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 
                        5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
                    ];
                    
                    $number = number_format($number, 2, '.', '');
                    $numberArray = explode('.', $number);
                    $wholeNumber = (int)$numberArray[0];
                    $decimalPart = (int)$numberArray[1];
                    
                    if ($wholeNumber == 0) {
                        return "Zero";
                    }
                    
                    $result = "";
                    
                    // Process thousands
                    if ($wholeNumber >= 1000) {
                        $thousands = (int)($wholeNumber / 1000);
                        $result .= convertLessThanOneThousand($thousands, $ones, $teens, $tens) . " Thousand ";
                        $wholeNumber %= 1000;
                    }
                    
                    // Process hundreds
                    if ($wholeNumber >= 100) {
                        $hundreds = (int)($wholeNumber / 100);
                        $result .= $ones[$hundreds] . " Hundred ";
                        $wholeNumber %= 100;
                    }
                    
                    // Process tens and ones
                    if ($wholeNumber > 0) {
                        if ($wholeNumber < 10) {
                            $result .= $ones[$wholeNumber];
                        } else if ($wholeNumber < 20) {
                            $result .= $teens[$wholeNumber] ?? $tens[1] . " " . $ones[$wholeNumber - 10];
                        } else {
                            $tensValue = (int)($wholeNumber / 10);
                            $onesValue = $wholeNumber % 10;
                            $result .= $tens[$tensValue];
                            if ($onesValue > 0) {
                                $result .= " " . $ones[$onesValue];
                            }
                        }
                    }
                    
                    return trim($result) . ($decimalPart > 0 ? " Ringgit And " . $decimalPart . "/100 Sen" : " Ringgit Only");
                }
                
                function convertLessThanOneThousand($number, $ones, $teens, $tens) {
                    $result = "";
                    
                    if ($number >= 100) {
                        $hundreds = (int)($number / 100);
                        $result .= $ones[$hundreds] . " Hundred ";
                        $number %= 100;
                    }
                    
                    if ($number > 0) {
                        if ($number < 10) {
                            $result .= $ones[$number];
                        } else if ($number < 20) {
                            $result .= $teens[$number] ?? $tens[1] . " " . $ones[$number - 10];
                        } else {
                            $tensValue = (int)($number / 10);
                            $onesValue = $number % 10;
                            $result .= $tens[$tensValue];
                            if ($onesValue > 0) {
                                $result .= " " . $ones[$onesValue];
                            }
                        }
                    }
                    
                    return trim($result);
                }
                
                echo numberToWords($payslip['net_pay']);
                ?>
                </p>
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