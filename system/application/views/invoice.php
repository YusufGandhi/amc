<HTML>
<HEAD>
<LINK REL="STYLESHEET" HREF="<?php echo $baseURL ?>css/invoice.css">
</HEAD>
<BODY>
<img src="<?php echo $baseURL ?>img/blank.gif" width="1" height="<?php print $HeaderDistance; ?>">
<DIV align="center">
  <TABLE border="0" width="600">
    <tr> 
      <TD valign="top" colspan="2"> <table>
          <tr> 
            <td align="left"> To:<br> <?php print $customer_name; ?><br> <?php print str_replace("\n", "<br>", $customer_address); ?> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <b>Date:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print "$date"; ?> 
            </td>
          </tr>
		            <tr> 
            <td align="right"> <b>Ref:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print "$customer_id"; ?> 
            </td>
          </tr>
          <tr> 
            <td> <br><br><table width="100%" CELLPADDING="2" CELLSPACING="0" border="1" bordercolor="<?PHP print "$BorderColour"; ?>">
                <tr bgcolor="<?PHP print "$TableBGcolour"; ?>"> 
                  <th colspan="2">Product</th>
                  <!--<th>Cost</th>
                  <th><?PHP print "$TaxName"; ?></th>-->
                  <th colspan="2">Total</th>
                </tr>
                <tr> 
                  <td><img src="<?php echo $baseURL ?>img/blank.gif" width="450" height="14"></td>
                  <td><img src="<?php echo $baseURL ?>img/blank.gif" width="60" height="14"></td>
                  <td><img src="<?php echo $baseURL ?>img/blank.gif" width="60" height="14"></td>
                  <td><img src="<?php echo $baseURL ?>img/blank.gif" width="60" height="14"></td>
                </tr>
<?php
$tot_cost = "0";
$tot_vat = "0";
$tot_price = "0";
/*
 *  This is the looping between item in the order list
 * 
 */
/*for($l=0; $l<sizeof($order); $l++){
	if(!strlen($order[$l]) && $l == (sizeof($order)-1)) { continue; }*/
?>
	                
					<!-- printing each  item in the order list -->
				<?php if ($price_obat > 0 ) : ?>
					<tr> 
	                  <td><?php echo "Obat" ?></td> <!-- this is to print the order's name -->
	                  <td align="right">&nbsp;</td>	                  
					  <td><?php print "$CurrencyUnit"; ?></td>
	                  <td align="right" width="50"><?php print number_format($price_obat,2); ?></td>
	                </tr>
				<?php endif; ?>
					<!-- end of print -->
	<?php
	/*$tot_cost += $price[$l]/($vat_at+100)*100;
	$tot_vat += $price[$l]-($price[$l]/($vat_at+100)*100);
	$tot_price += $price[$l];*/
//}
// --- end of the loop between order list --//
?>

                <tr> 
                  <td colspan="4"><?php if($DisplayVat == "Y") { print "<i>$TaxName charged at a rate of $vat_at%</i>"; } else print "<img src=\"<?php echo $baseURL ?>img/blank.gif\" width=\"1\" height=\"14\">";?></td>
                </tr>
              </table>
              <br> <table align="right" width="0" CELLPADDING="2" CELLSPACING="1" border="1" bordercolor="<?PHP print "$BorderColour"; ?>">
                <tr> 
                  <td width="60" align="right"><b><?php print "$CurrencyUnit"; ?><?php print number_format($tot_cost,2); ?></b></td>
                  <td align="right" width="60"><b><?php print "$CurrencyUnit"; ?><?php print number_format($tot_vat,2); ?></b></td>
                  <td align="right" width="60"><b><?php print "$CurrencyUnit"; ?><?php print number_format($tot_price,2); ?></b></td>
                </tr>
              </table> <br> <br><br><br><?php //print tag($notes); ?>
              <br> <br> <?php print "$InvoiceSignature"; ?> </td>
          </tr></table></TD></TR></TABLE>
</DIV>
</BODY>
</HTML>