@include('emails.header')

<!-- 2 Column Images & Text Side by SIde -->
<table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth" bgcolor="#202022">
    <tr>
        <td style="padding:10px 0">
                <table align="left" width="29%" cellpadding="0" cellspacing="0" border="0" class="deviceWidth">
                    <tr>
                        <td valign="top" align="center" class="center" style="padding-top:20px">
                                <a href="#"><img width="100" src="{{ $message->embed( 'http://i.imgur.com/lrERG6t.png' ) }}" alt="" border="0" style="border-radius: 4px; width: 100px; display: block;" class="deviceWidth" /></a>
                        </td>
                    </tr>
                </table>                       
                <table align="right" width="70%" cellpadding="0" cellspacing="0" border="0" class="deviceWidth">
                    <tr>
                        <td style="font-size: 12px; color: #959595; font-weight: normal; text-align: left; font-family: Georgia, Times, serif; line-height: 24px; vertical-align: top; padding:10px 8px 10px 8px">

                            <table>
                                <tr>
                                    
                                    <td valign="middle" style="padding:0 10px 10px 0"><a href="#" style="text-decoration: none; font-size: 16px; color: #ccc; font-weight: bold; font-family:Arial, sans-serif ">You've received a payment request</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="mso-table-lspace:0;mso-table-rspace:0; margin:0">  
                                Hey {{ $recipient->first_name }},
                                You've received a payment request over at GrindsHub for a grind you got recently. Just click on the below link to head on over and pay the balance.
                                <br/><br/>

                                <table width="100" align="right">
                                    <tr>
                                        <td background="http://www.emailonacid.com/images/blog_images/Emailology/2013/free_template_1/blue_back.jpg" bgcolor="#409ea8" style="padding:5px 0;background-color:#409ea8; border-top:1px solid #77d5ea; background-repeat:repeat-x" align="center">
                                            <a href="{{ $recipient->url }}" 
                                            style="
                                            color:#ffffff;
                                            font-size:13px;
                                            font-weight:bold;
                                            text-align:center;
                                            text-decoration:none;
                                            font-family:Arial, sans-serif;
                                            -webkit-text-size-adjust:none;">
                                                    View Request
                                            </a>

                                        </td>
                                    </tr>
                                </table>

                            </p>
                        </td>
                    </tr>
                </table>  
             
        </td>
    </tr>
    <tr>
        <td bgcolor="#fe7f00"><div style="height:6px">&nbsp;</div></td>
    </tr>                
</table><!-- End 2 Column Images & Text Side by SIde -->

@include('emails.footer');