<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Notifikasi</title>
    <style>
      /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */
      
      /*All the styling goes here*/
      
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%; 
      }

      body {
        background-color: #f6f6f6;
        font-family: sans-serif;
        -webkit-font-smoothing: antialiased;
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%; 
      }

      table {
        border-collapse: separate;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width: 100%; }
        table td {
          font-family: sans-serif;
          font-size: 14px;
          vertical-align: top; 
      }

      /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

      .body {
        background-color: #f6f6f6;
        width: 100%; 
      }

      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
      .container {
        display: block;
        margin: 0 auto !important;
        /* makes it centered */
        max-width: 580px;
        padding: 10px;
        width: 580px; 
      }

      /* This should also be a block element, so that it will fill 100% of the .container */
      .content {
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 580px;
        padding: 10px; 
      }

      /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
      .main {
        background: #ffffff;
        border-radius: 3px;
        width: 100%; 
      }

      .wrapper {
        box-sizing: border-box;
        padding: 20px; 
      }

      .content-block {
        padding-bottom: 10px;
        padding-top: 10px;
      }

      .footer {
        clear: both;
        margin-top: 10px;
        text-align: center;
        width: 100%; 
      }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
          color: #999999;
          font-size: 12px;
          text-align: center; 
      }

      /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
      h1,
      h2,
      h3,
      h4 {
        color: #000000;
        font-family: sans-serif;
        font-weight: 400;
        line-height: 1.4;
        margin: 0;
        margin-bottom: 30px; 
      }

      h1 {
        font-size: 35px;
        font-weight: 300;
        /* text-align: center; */
        text-transform: capitalize; 
      }

      p,
      ul,
      ol {
        font-family: sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0;
        margin-bottom: 15px; 
      }
        p li,
        ul li,
        ol li {
          list-style-position: inside;
          margin-left: 5px; 
      }

      a {
        color: #3498db;
        text-decoration: underline; 
      }

      /* -------------------------------------
          BUTTONS
      ------------------------------------- */
      .btn {
        box-sizing: border-box;
        width: 100%; }
        .btn > tbody > tr > td {
          padding-bottom: 15px; }
        .btn table {
          width: auto; 
      }
        .btn table td {
          background-color: #ffffff;
          border-radius: 5px;
          text-align: center; 
      }
        .btn a {
          background-color: #ffffff;
          border: solid 1px #3498db;
          border-radius: 5px;
          box-sizing: border-box;
          color: #3498db;
          cursor: pointer;
          display: inline-block;
          font-size: 14px;
          font-weight: bold;
          margin: 0;
          padding: 12px 25px;
          text-decoration: none;
          text-transform: capitalize; 
      }

      .btn-primary table td {
        background-color: #3498db; 
      }

      .btn-primary a {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff; 
      }

      /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
      .last {
        margin-bottom: 0; 
      }

      .first {
        margin-top: 0; 
      }

      .align-center {
        text-align: center; 
      }

      .align-right {
        text-align: right; 
      }

      .align-left {
        text-align: left; 
      }

      .clear {
        clear: both; 
      }

      .mt0 {
        margin-top: 0; 
      }

      .mb0 {
        margin-bottom: 0; 
      }

      .preheader {
        color: transparent;
        display: none;
        height: 0;
        max-height: 0;
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        mso-hide: all;
        visibility: hidden;
        width: 0; 
      }

      .powered-by a {
        text-decoration: none; 
      }

      hr {
        border: 0;
        border-bottom: 1px solid #f6f6f6;
        margin: 20px 0; 
      }

      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 620px) {
        table.body h1 {
          font-size: 28px !important;
          margin-bottom: 10px !important; 
        }
        table.body p,
        table.body ul,
        table.body ol,
        table.body td,
        table.body span,
        table.body a {
          font-size: 16px !important; 
        }
        table.body .wrapper,
        table.body .article {
          padding: 10px !important; 
        }
        table.body .content {
          padding: 0 !important; 
        }
        table.body .container {
          padding: 0 !important;
          width: 100% !important; 
        }
        table.body .main {
          border-left-width: 0 !important;
          border-radius: 0 !important;
          border-right-width: 0 !important; 
        }
        table.body .btn table {
          width: 100% !important; 
        }
        table.body .btn a {
          width: 100% !important; 
        }
        table.body .img-responsive {
          height: auto !important;
          max-width: 100% !important;
          width: auto !important; 
        }
      }

      /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
      @media all {
        .ExternalClass {
          width: 100%; 
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height: 100%; 
        }
        .apple-link a {
          color: inherit !important;
          font-family: inherit !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          line-height: inherit !important;
          text-decoration: none !important; 
        }
        #MessageViewBody a {
          color: inherit;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
          font-weight: inherit;
          line-height: inherit;
        }
        .btn-primary table td:hover {
          background-color: #34495e !important; 
        }
        .btn-primary a:hover {
          background-color: #34495e !important;
          border-color: #34495e !important; 
        } 
        .center-logo{
            text-align: center;
        }
        .center-logo img{
            width: 250px;
        }
        .heading_text{
            font-weight: bold;
            font-size: 20px;
        }
      }
      .tabel_pesanan{
        background-color: #ececec;
        padding: 20px;
        border-radius: 10px;
      }

    </style>
  </head>
  <body>
    <span class="preheader">Pesan baru dari thriftex.id</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">
            <div class="center-logo">
                <img src="" alt="">
            </div>
            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr class="" style="background-image: url('https://thriftex.id/assets/front/images/thrift/mainbg.jpeg');height:90px;display:flex;text-align:center;">
                <td style="text-align: center;width:100%;padding-top:30px;font-size:25px;color:#fff" rowspan="3"><b>THRIFTEX</b></td>
              </tr>
              <tr>
                <td class="wrapper">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <h1 class="heading_text">Status Legit Check Anda</h1>
                        <p>Case ID : #<?= $case_id ?></p>
                        <?php
                          $description_result = '';
                          if($data_legit_detail->check_result == 'real'){
                              $description_result = 'Terima kasih telah menggunakan layanan legit check kami. Setelah menganalisis produk yang Anda ingin verifikasi, kami dengan senang hati memberitahukan bahwa produk tersebut teridentifikasi sebagai produk original. Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami kembali.';
                          }elseif($data_legit_detail->check_result == 'fake'){
                              $description_result = 'Terima kasih telah menggunakan layanan legit check kami. Setelah menganalisis produk yang Anda ingin verifikasi, kami dengan berat hati memberitahukan bahwa produk tersebut teridentifikasi sebagai produk fake. Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami kembali.';
                          }elseif($data_legit_detail->check_result == 'processing'){
                            if($data_legit_detail->processing_status =='no_brand_info'){
                              $description_result = 'Terima kasih telah menggunakan layanan legit check kami.  Kami mohon maaf, saat ini tim kami belum memiliki pengetahuan yang cukup tentang brand tersebut. Kami akan terus memperluas pengetahuan kami untuk mengakomodasi lebih banyak merek. Apabila Anda memiliki produk lain yang ingin diperiksa, silakan ajukan kembali dan kami akan melakukan yang terbaik untuk memberikan hasil analisis yang akurat. ';
                            }elseif($data_legit_detail->processing_status =='no_product_info'){
                              $description_result = 'Terima kasih telah menggunkan layanan legit check kami, setelah melakukan analisis awal, kami perlu menyampaikan bahwa research atau bukti yang kami temukan terkait produk ini belum cukup kuat untuk memberikan keputusan akhir terkait keaslian produk yang Anda ingin verifikasi. Kami memahami kepentingan memastikan keaslian produk dengan tepat dan kami sedang melanjutkan upaya untuk mendapatkan informasi lebih lanjut. Kami akan memberikan pembaruan segera setelah kami memiliki hasil yang lebih lengkap.';
                            }elseif($data_legit_detail->processing_status =='no_detail_picture'){
                              $description_result = 'Terima kasih telah menggunakan layanan legit check Thriftex! Kami mohon maaf, namun kami tidak dapat melakukan verifikasi yang akurat karena foto yang Anda berikan kurang lengkap. Untuk melakukan analisis yang mendalam, kami memerlukan foto yang jelas dan menyeluruh dari berbagai sudut, termasuk detail penting yang terkait dengan keaslian produk. Jika Anda membutuhkan panduan lebih lanjut, jangan ragu untuk menghubungi kami';
                            }
                          }
                        ?>
                        <p><?= $description_result ?></p>
                        <br>
                        <table>
                            <tr>
                                <td>Nama Item</td>
                                <td>:</td>
                                <td><?= $data_legit_detail->nama_item ?></td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>:</td>
                                <td><?= $data_legit_detail->nama_brand ?></td>
                            </tr>
                            <tr>
                                <td width="40%">Status Legit Check</td>
                                <td>:</td>
                                <?php
                                $status_item = '';
                                if($data_legit_detail->check_result == 'real'){
                                    $status_item = 'Original';
                                }elseif($data_legit_detail->check_result == null){
                                    $status_item = 'Waiting';
                                }elseif($data_legit_detail->check_result == 'fake'){
                                    $status_item = 'Fake';
                                }elseif($data_legit_detail->check_result == 'processing'){
                                  if($data_legit_detail->processing_status =='no_brand_info'){
                                    $status_item = 'NO BRAND INFORMATION';
                                  }elseif($data_legit_detail->processing_status =='no_product_info'){
                                    $status_item = 'NO PRODUCT INFORMATION';
                                  }elseif($data_legit_detail->processing_status =='no_detail_picture'){
                                    $status_item = 'NO DETAIL PICTURE';
                                  }
                                }
                                ?>
                                <td><?= $status_item ?></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                        <br>
                        <hr>
                        <p>Terima Kasih,</p>
                        <p><i><small>thriftex.id</small></i></p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            <!-- START FOOTER -->
            <div class="footer">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block">

                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by">
                    &copy; 2023 <a href="#">thriftex.id</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>