<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="text-end mb-3">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-2"></i> Print Payslip
        </button>
        <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary ms-2">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    
    <div class="card" id="payslip">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3>Payslip For <?= $month ?> <?= $year ?></h3>
                <h4><?= strtoupper($company['name']) ?></h4>
                <p class="small"><?= $company['ssm_number'] ? '(' . $company['ssm_number'] . ')' : '' ?></p>
            </div>
            
            <div class="row mb-2">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 40%" class="ps-0 py-1">Pay Date</td>
                            <td class="py-1">: <?= date('Y/m/d', strtotime($pay_date)) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0 py-1">Working Days</td>
                            <td class="py-1">: <?= $working_days ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 40%" class="ps-0 py-1">Employee ID</td>
                            <td class="py-1">: <?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0 py-1">Name</td>
                            <td class="py-1">: <?= strtoupper($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <table class="table table-sm table-bordered">
                        <tr class="table-secondary">
                            <th style="width: 30%">Earnings</th>
                            <th style="width: 20%">Amount (RM)</th>
                            <th style="width: 30%">Deductions</th>
                            <th style="width: 20%">Amount (RM)</th>
                        </tr>
                        <tr>
                            <td>Basic Pay</td>
                            <td class="text-end"><?= number_format($basic_pay, 2) ?></td>
                            <td>EPF Employee</td>
                            <td class="text-end"><?= number_format($epf_employee, 2) ?></td>
                        </tr>
                        <tr>
                            <td>Allowance</td>
                            <td class="text-end"><?= number_format($allowance, 2) ?></td>
                            <td>SOCSO Employee</td>
                            <td class="text-end"><?= number_format($socso_employee, 2) ?></td>
                        </tr>
                        <tr>
                            <td>Overtime</td>
                            <td class="text-end"><?= number_format($overtime, 2) ?></td>
                            <td>EIS Employee</td>
                            <td class="text-end"><?= number_format($eis_employee, 2) ?></td>
                        </tr>
                        <tr>
                            <td class="border-bottom-0"></td>
                            <td class="border-bottom-0"></td>
                            <td>PCB</td>
                            <td class="text-end"><?= number_format($pcb, 2) ?></td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>Total Earnings</strong></td>
                            <td class="text-end"><strong><?= number_format($total_earnings, 2) ?></strong></td>
                            <td class="text-end"><strong>Total Deductions</strong></td>
                            <td class="text-end"><strong><?= number_format($total_deductions, 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Net Pay</strong></td>
                            <td class="text-end"><strong><?= number_format($net_pay, 2) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="text-center mb-2 border p-2 bg-light">
                <h5 class="mb-1">RM <?= number_format($net_pay, 2) ?></h5>
                <p class="mb-0 small">
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
                
                echo numberToWords($net_pay);
                ?>
                </p>
            </div>
            
            <div class="row mt-3">
                <div class="col-6 text-center">
                    <div class="signature-line"></div>
                    <p class="mb-0 small">Employer Signature</p>
                </div>
                <div class="col-6 text-center">
                    <div class="signature-line"></div>
                    <p class="mb-0 small">Employee Signature</p>
                </div>
            </div>
            
            <div class="text-center mt-2">
                <p class="mb-0 small text-muted">This is system generated payslip</p>
            </div>
        </div>
    </div>
</div>

<style>
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
            max-height: 50vh;
            border: none;
            /* Scale content to fit half page */
            transform-origin: top left;
            transform: scale(0.9);
            overflow: hidden;
        }
        .card {
            border: none !important;
        }
        /* Reduce font sizes */
        #payslip h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        #payslip h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        #payslip p, #payslip td, #payslip th {
            font-size: 12px;
        }
        /* Reduce padding and margins */
        #payslip .card-body {
            padding: 10px;
        }
        #payslip .table td, #payslip .table th {
            padding: 4px 8px;
        }
        #payslip .row {
            margin-bottom: 10px !important;
        }
        /* Add a dashed line at the bottom to indicate where to cut */
        #payslip:after {
            content: "";
            display: block;
            position: fixed;
            bottom: 50vh; /* 50% of viewport height */
            left: 0;
            width: 100%;
            border-bottom: 2px dashed #000;
        }
        /* Hide any overflow */
        @page {
            size: A4;
            margin: 0;
        }
        html, body {
            height: 100%;
            margin: 0 !important;
            padding: 0 !important;
        }
    }
    
    /* Style for the signature lines */
    .signature-line {
        border-top: 1px solid #000;
        width: 80%;
        margin: 40px auto 5px;
    }
</style>
<?= $this->endSection() ?>