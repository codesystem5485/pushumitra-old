<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    
<title>Invoice </title>

</head>
<style type="text/css">
* { margin: 0; padding: 0; }
body {font-size:14px; font-family:Arial, serif;}
@font-face {
  font-family: 'Droid Arabic Kufi';
  font-style: normal;
  font-weight: 400;
  
}

@font-face {
  font-family: 'Droid Arabic Kufi';
  font-style: normal;
  font-weight: 700;
  
}
</style>
<body>

    @php
        // Calculate basic amount (amount without GST)
        // Since amount includes 18% GST, basic amount = amount / 1.18
        $basic_amount = $amount / 1.18;
        $tax_amount = $amount - $basic_amount;
        
        // Determine GST type based on user_state_id
        $is_same_state = (isset($user_state_id) && $user_state_id == 22);
        
        if ($is_same_state) {
            // Same state: CGST 9% + SGST 9%
            $cgst_percentage = 9;
            $sgst_percentage = 9;
            $cgst_amount = $tax_amount / 2;
            $sgst_amount = $tax_amount / 2;
            $igst_percentage = 0;
            $igst_amount = 0;
        } else {
            // Different state or empty: IGST 18%
            $cgst_percentage = 0;
            $sgst_percentage = 0;
            $cgst_amount = 0;
            $sgst_amount = 0;
            $igst_percentage = 18;
            $igst_amount = $tax_amount;
        }
        
        // Format numbers to 2 decimal places
        $basic_amount = number_format($basic_amount, 2, '.', '');
        $cgst_amount = number_format($cgst_amount, 2, '.', '');
        $sgst_amount = number_format($sgst_amount, 2, '.', '');
        $igst_amount = number_format($igst_amount, 2, '.', '');
        $tax_amount = number_format($tax_amount, 2, '.', '');
        $amount = number_format($amount, 2, '.', '');
    @endphp

    <div class="page-wrap" style="max-width:1000px;width:100%;margin-left:20px auto;margin-right:20px">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:4px;font-size:14px; font-family:Arial, serif;">
            
            <tr>
                <td style="border:none;font-size:12px;color:#777;padding:5px;text-align: center;">
                    <img id="image" src="{{ public_path('logo.jpg') }}" alt="logo" width="350" />
                </td>
            </tr>
            <tr><td style="text-align:center;"><h3>GSTIN : 27ATLPM2646L1Z6</h3></td> </tr>
            <tr><td style="height:20px"></td> </tr>
          <tr><td style="height:20px"></td> </tr>
        </table>

        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:14px; font-family:Arial, serif;margin-left:20px;">
            <tr>
                <td style="width:70%;">&nbsp;</td>
                <td style="font-size:14px;color:#353535;border:none;padding:5px;width:30%;">
                    <!--<label style="font-weight:600;display:inline-block;line-height:22px">Invoice Order:&nbsp;&nbsp;</label> 231664<br>
                    
                    <label style="font-weight:600;display:inline-block;line-height:22px">Details:&nbsp;&nbsp;</label>02-6366366<br>
                    -->
                    <label style="font-weight:600;display:inline-block;line-height:22px">Invoice No:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">{{$invoice_number}}</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">Date:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">{{$invoice_date}}</label>
                </td>
            </tr>
            <tr><td colspan="2" style="height:10px"></td> </tr>
            <tr>
                <td colspan="2"><strong>To,</strong><br>
                {{$user_name}}
                </td> 
            </tr>
            <tr><td colspan="2" style="height:20px"></td> </tr>
            <tr><td colspan="2" style=""><strong>Phone no:</strong></td> </tr>
            <tr><td colspan="2" style="height:20px">{{$mobile_number}}</td> </tr>
        </table>

        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px; font-family:Arial, serif;margin-left:20px;margin-right:20px">

            <thead>
                <tr>
                    <td style="font-size:14px;line-height:15px;padding:8px 8px;text-align:center;border:1px solid #adadad;border-right:none;background:#eee;"><strong>No</strong></td>
                    <td style="font-size:14px;line-height:15px;padding:8px 8px;text-align:center;border:1px solid #adadad;border-right:none;background:#eee;width: 340px;"><strong>Order Details </strong></td>
                    <td style="font-size:14px;line-height:15px;padding:8px 8px;text-align:center;border:1px solid #adadad;border-right:none;background:#eee;"><strong>Price</strong></td>
                    <td style="font-size:14px;line-height:15px;padding:8px 8px;text-align:center;border:1px solid #adadad;background:#eee;line-height:16px;"><strong>Total</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad">1</td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad">{{$type}}</td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;">{{$basic_amount}}<strong></strong></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$basic_amount}}</td>
                </tr>
                
                @if($is_same_state)
                    {{-- Same State: Show CGST and SGST --}}
                    <tr>
                        <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">CGST @ {{$cgst_percentage}}%</td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$cgst_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">SGST @ {{$sgst_percentage}}%</td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$sgst_amount}}</td>
                    </tr>
                @else
                    {{-- Different State or Empty: Show IGST --}}
                    <tr>
                        <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">IGST @ {{$igst_percentage}}%</td>
                        <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$igst_amount}}</td>
                    </tr>
                @endif
                
                <tr>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">Grand Total</td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$amount}}</td>
                </tr>
                <tr>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">Amount Paid</td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad">{{$amount}}</td>
                </tr>
                <!--<tr>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;">Over Paid </td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad"></td>
                </tr>
                <tr>
                    <td style="padding:6px 8px;border-left:1px solid #adadad; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px; border-bottom:1px solid #adadad"></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;background-color: #ededed;"><strong>Final Total </strong></td>
                    <td style="padding:6px 8px;border-left:1px solid #adadad;border-bottom:1px solid #adadad;border-right:1px solid #adadad"></td>
                </tr>-->

            </tbody>
        </table>
        
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:14px; font-family:Arial, serif;margin-left:20px;margin-right:10px">
            <tr><td style="height:30px"></td> </tr>
            <tr>
                <td style="font-size:14px;color:#353535;border:none;padding:5px;width:30%;">
                    <label style="font-weight:600;display:inline-block;line-height:22px"><strong>Account Details:</strong>&nbsp;&nbsp;</label><br><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">Bank name:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">IDBI BANK</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">A/c No:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">0573102000021960</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">Account Name:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">Pashumitra Enterprises</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">Branch:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px">Dwarka Circle Nashik</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">IFSCode:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px"> IBKL0000573</label><br>
                    <label style="font-weight:600;display:inline-block;line-height:22px">PAN No:&nbsp;&nbsp;</label><label style="font-weight:600;display:inline-block;line-height:22px"> ATLPM2646L</label><br>
                </td>
            </tr>
            <tr>
                <td style="font-size:14px;color:#353535;border:none;padding:5px;">
                    <label style="font-weight:600;display:inline-block;line-height:22px"><strong>Terms and Condition:</strong></label>
                    <br>
                    * Out station clients should pay through "AT PAR CHEQUE" For Speedy processing <br>
                    * cheque payments are subjected to realization <br>
                </td>
            </tr>
            <tr><td style="height:20px"></td> </tr>
            <tr><td style="text-align: right;margin-right:20px;"><strong>E & O E</strong></td></tr>
            <tr><td style="height:20px"></td> </tr>
            <tr><td style=""><strong>Thank You, </strong></td></tr>
            <tr><td style="">PASHUMITRA ENTERPRISES </td> </tr>
            <tr><td style="height:30px"></td> </tr>
        </table>

        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:14px; font-family:Arial, serif;margin-left:20px;">
            <tr>
              <td style="line-height:20px;margin-left:20px;"><label style="">Signature</label><br><label style=""><strong>{{$user_code}}</strong></label></td>
              <td style="line-height:20px;text-align: center;">Signature(Receiver)<br><strong>{{$user_name}}</strong></td>
          </tr>
          <tr><td colspan="2" style="height:30px"></td> </tr>
          <tr><td colspan="2" style="border-top: 1px solid #adadad;height:10px"></td> </tr>
          <tr>
            <td colspan="2" style="text-align: center;line-height: 25px;">8, Priyanka Nest Apartment, Near Govind Nagar Jogging Track, Sadguru Nagar, Nashik - 422009 <br></td>
          </tr>
          <tr>  
            <td colspan="2" style="text-align: center;line-height: 25px;"><strong>Website:</strong>&nbsp;&nbsp;pashumitra.com<br></td>
          </tr>
          <tr>  
            <td colspan="2" style="text-align: center;line-height: 25px;"><strong>Email id:</strong>&nbsp;&nbsp;pashumitraenterprises@gmail.com<br></td>
          </tr>
          <tr>  
            <td colspan="2" style="text-align: center;line-height: 25px;"><strong>Mob:</strong>&nbsp;&nbsp;8805700570<br></td>
        </tr>
        </table>
                
    </div>

</body>

</html>
