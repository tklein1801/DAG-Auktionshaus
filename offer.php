<!DOCTYPE html>
<?php require_once '/var/www/vhosts/dulliag.de/httpdocs/assets/php/system.php'; ?>
<html>
  <head>
    <?php require_once $comp_header; ?>
    <link rel="stylesheet" href="<?php echo $url_css.'auction.css'; ?>">
  </head>
  <body>
    <?php
      require_once $comp_cookieDisclaimer;
      require_once $comp_contextMenu;
      require_once $comp_preLoader;
      require_once $comp_auctionbar;
      require_once $comp_navbar;
    ?>

    <div class="page-container">
      <div id="breadcrumb-container" class="w-100 mx-auto">
        <ul id="breadcrumb" class="mb-0 p-0">
          <li class="float-left">
            <a class="text-center" href="https://dulliag.de">
              <i class="fas fa-server"></i>
              DulliAG
            </a>
          </li>

          <li class="float-left">
            <a class="text-center" href="https://dulliag.de/Auktionen/">
              <i class="fas fa-ticket-alt"></i>
              Auktionen
            </a>
          </li>

          <li class="float-left">
            <a class="text-center mr-0" href="https://dulliag.de/Auktionen/">
              <i class="fas fa-envelope-open-text"></i>
              Angebot
            </a>
          </li>
        </ul>
      </div><!-- ./breadcrumb-container -->

      <div class="section">
        <!--
          Offers
        -->
        <div class="container px-0">
          <div class="row">
            <div class="col-md-8">
              <div class="w-100 mb-3">
                <!-- Get src of thumbnail from system.js -->
                <img id="thumbnailPlaceholder" class="w-100 rounded" alt="Produktbild">
              </div><!-- ./thumbnail -->

              <div id="productImages" class="product-images w-100 mb-3">
                <!--
                  Get product images via system.js
                -->
              </div><!-- ./product-images -->
            </div><!-- ./column -->

            <div class="col-md-4">
              <div class="rounded bg-light mb-3 p-3">
                <div class="header">
                  <div class="row">
                    <p id="sellerInformation" class="text">Lädt...</p>
                    <button class="btn btn-sm btn-success ml-auto" data-toggle="modal" data-target="#contactSellerModal">Kontaktieren</button>
                  </div>
                </div><!-- ./header -->
              </div>

              <div class="rounded bg-light p-3">
                <h4 id="offerTitle" class="title">Lädt...</h4>

                <h6 id="timeLeft" class="title py-0">Lädt...</h6>
                <h5 id="curBid" class="title py-0">Lädt...</h5>
                <div id="orderOutput" class="row">
                  <!--
                    Get content via js
                  -->
                </div><!-- ./row -->

                <strong>Beschreibung</strong>
                <p id="offerDesc" class="text">
                  <!--
                    Get description from js
                  -->
                </p>
              </div><!-- ./bg-light -->
            </div><!-- ./column -->
          </div>
        </div><!-- ./container -->
      </div><!-- ./support -->

      <?php require_once $comp_footer;?>
    </div><!-- ./page-container -->

    <?php
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/contactSeller.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/apiKey.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/signInModal.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/signUpModal.php';
    ?>

    <?php require_once $comp_script; ?>
    <script>
      activeMenuItem('auctionLink');
    </script><!-- ./navbarJS -->

    <!-- This page script -->
    <script src="https://files.dulliag.de/web/js/auction.js"></script>
    <script type="text/javascript">
      const ls = localStorage;
      const auction = new Auction;
      const rlapi = new ReallifeAPI;
      const url = new URL(window.location.href);
      const offerID = url.searchParams.get("id");
      const loginCookie = getCookie("username"); // Returns string
      const oData = auction.getOffer(offerID); // Returns string
      const now = new Date().getTime() / 1000; // Returns string

      /**
       * Get & set offer data
       * Seller => ProfileIMG, Name
       * Product => Thumbnail, Product images, Title, Description, Price, Time
       * You can only bid/buy this product if it's not bought & it's not expires & u are not the owner
       *
       * Get & set profile data only if user is logged in
       */
      const offerType = oData[0];
      // const offerID = oData[1]; is already declared in top
      const sellerAvatar = "<?php echo $dagLogo; ?>";
      const sellerSteamID = oData[2];
      const sellerName = oData[3];
      const offerTitle = oData[4];
      const offerDesc = oData[5];
      const offerPrice = parseInt(oData[6]);
      const expireDate = oData[7];
      const boughtStatus = parseInt(oData[8]);
      const boughtBy = oData[9];
      const thumbnail = auction.getThumbnail(offerID); // Returns array
      var thumbnailURL = thumbnail[4];
      const productImages = auction.getProductImage(offerID); // Returns array

      // Set seller information
      $("#sellerInformation").html(`<img style="width: 2.5rem; height: auto;" class="rounded-circle shadow-md" src="${sellerAvatar}" alt="Profilbild"> ${sellerName}`);
      // Set product information
      $("#offerTitle").text(offerTitle);
      $("#offerDesc").html(offerDesc);
      // Set thumbnail
      $("#thumbnailPlaceholder").attr("src", thumbnailURL);
      $("#productImages").append(`<a class="image-toggle mr-3" data-image="${thumbnailURL}"><img class="rounded" src="${thumbnailURL}" alt="Vorschaubild"></a>`);
      // Set product images
      if(productImages.length > 0) {
        productImages.forEach(data => {
          var imageURL = data[4];
          $("#productImages").append(`<a class="image-toggle mr-3" data-image="${imageURL}"><img class="rounded" src="${imageURL}" alt="Produktbild"></a>`);
        });
        var lastImage = $(".image-toggle").last();
        $(lastImage).removeClass("mr-3");
      }
      // Check if offer is not expired & wasn't bought already
      var countdown = setInterval(() => {
        if(boughtStatus != 1) {
          var endDate = expireDate;
          var now = new Date().getTime();
          now = now / 1000;
          var timeLeft = endDate - now;
          if(timeLeft > 0) {
            var d = Math.floor(timeLeft / 86400);
            var h = Math.floor((timeLeft - (d * 86400)) / 3600);
            var m = Math.floor((timeLeft - (d * 86400) - (h * 3600 )) / 60);
            var s = Math.floor((timeLeft - (d * 86400) - (h * 3600) - (m * 60)));
            if(d != 0) {
              if(d > 1) {
                $("#timeLeft").text(`Verbleibende Zeit: ${d} Tage und ${h}:${m}:${s}`);
              } else {
                $("#timeLeft").text(`Verbleibende Zeit: ${d} Tag und ${h}:${m}:${s}`);
              }
            } else {
              $("#timeLeft").text(`Verbleibende Zeit: ${h}:${m}:${s}`);
            }
          } else {
            clearInterval(countdown);
            $("#timeLeft").text("Das Angebot ist nicht mehr verfügbar");
          }
        } else {
          $("#timeLeft").text("Das Angebot ist nicht mehr verfügbar");
        }

      }, 1000);
      // Get & set price
      switch (offerType) {
        case "1": // Sale
          // Just set price from db
          $("#curBid").text(`Preis ${offerPrice.toLocaleString(undefined)} NHD`);             
          break;
      
        case "2": // Auction
          // Check if someone has bid on this product & check if bid is higher as default product price
          const bids = auction.getBids(offerID);
          var highBid;
          if(bids.length > 0) {
            highBid = parseInt(bids[0][2]);
          } else {
            highBid = -1;
          }
          if(offerPrice > highBid) {
            $("#curBid").text(`Preis ${offerPrice.toLocaleString(undefined)} NHD`);
          } else {
            $("#curBid").text(`Preis ${highBid.toLocaleString(undefined)} NHD`);
          }
          break;
      }
      setInterval(() => {
        const newData = auction.getOffer(offerID);
        const curPrice = parseInt(newData[6]);
        switch (offerType) {
          case "1": // Sale
            // Just set price from db
            $("#curBid").text(`Preis ${curPrice.toLocaleString(undefined)} NHD`);             
            break;
        
          case "2": // Auction
            // Check if someone has bid on this product & check if bid is higher as default product price
            const bids = auction.getBids(offerID);
            var highBid;
            if(bids.length > 0) {
              highBid = parseInt(bids[0][2]);
            } else {
              highBid = -1;
            }
            if(curPrice > highBid) {
              $("#curBid").text(`Preis ${curPrice.toLocaleString(undefined)} NHD`);
            } else {
              $("#curBid").text(`Preis ${highBid.toLocaleString(undefined)} NHD`);
            }
            break;
        }
      }, 5000); // Get every 5 seconds the current product price
      


      // Check if user is logged in
      if(loginCookie != "" && loginCookie != " " && loginCookie != null) {
        // User is logged in
        auction.updateBar();

        // Check if API-Key is set
        if(ls.hasOwnProperty("apiKey")) {
          const apiKey = ls.getItem("apiKey");
          // Check if API-Key is empty
          if(apiKey != "" && apiKey != " ") {
            const userData = rlapi.getProfile(apiKey);
            const userSteamID = userData[15];
            const userRPName = userData[2];
            const cash = parseInt(userData[3]);
            const bankAcc = parseInt(userData[4])
            const curBal = bankAcc + cash;
            
            // Set auction bar content
            $("#playerBankAcc").text(`${bankAcc.toLocaleString(undefined)} NHD`);
            $("#playerCash").text(`${cash.toLocaleString(undefined)} NHD`);

            // Check if offer is expired
            if(now <= expireDate && boughtStatus != 1 && sellerSteamID != userSteamID) {
              switch (offerType) {
                case "1": // Buy
                  $("#orderOutput").append(`<button id="orderProduct" class="btn btn-sm btn-success w-100">Kaufen</button>`);                  
                  
                  // EventListener
                  $("#orderProduct").on("click", (event) => {
                    if(curBal >= offerPrice) {
                      const buy = auction.buy(offerID, userSteamID);
                      if(buy == true) {
                        toastr.success("Du hast das Produkt gekauft");
                        clearInterval(countdown);
                        $("#timeLeft").text("Das Angebot ist nicht mehr verfügbar");
                        $("#orderProduct").attr("disabled", true);
                        $("#orderProduct").html(`<s>Kaufen></s>`);
                      } else {
                        toastr.error("Etwas ist schief gelaufen");
                      }
                    } else {
                      toastr.error("Du hast nicht genügend Geld");
                    }
                  });
                  break;
              
                case "2": // Bid
                  $("#orderOutput").append(`<div class="input-group mb-3"><input type="number" id="bidInput" class="form-control rounded" inputmode="numeric" placeholder="Gebot eingeben..."><div class="input-group-append"><span class="input-group-text" style="border-radius: 0!important;">.00 NHD</span><button type="button" id="orderProduct" class="btn btn-success" style="padding: .375rem .75rem!important;">Bieten</button></div></div>`);
                  
                  // EvenListener
                  $("#orderProduct").on("click", (event) => {                    
                    const bids = auction.getBids(offerID);
                    var prodPrice = offerPrice;
                    var highBid;
                    var bid = parseInt($("#bidInput").val());
                    if(bids.length > 0) {
                      highBid = parseInt(bids[0][2]);
                    } else {
                      highBid = -1;
                    }
                    if(highBid > prodPrice) {
                      prodPrice = highBid;
                    } 

                    if(bid >= curBal) {
                      if(bid > prodPrice) {
                        const myBid = auction.bid(offerID, userSteamID, bid);
                        if(myBid == true) {
                          toastr.success("Dein Gebot wurde eingereicht");
                          $("#curBid").text(`Preis: ${bid.toLocaleString(undefined)} NHD`);
                          $("#bidInput").val("");
                        } else {
                          toastr.error("Etwas ist schief gelaufen");
                        }
                      } else {
                        toastr.error("Dein Gebot muss höher sein");
                      }
                    } else {
                      toastr.error("Du hast nicht genügend Geld");
                    }
                  });
                  break;
              }                            
            } else { // Offer is expired, bought or seller wants to buy his own product
              switch (offerType) {
                case "1": // Buy
                  // Append disabled button
                  $("#orderOutput").append(`<button id="orderProduct" class="btn btn-sm btn-success w-100" disabled><s>Kaufen</s></button>`);                  
                  break;
              
                case "2": // Bid
                  // Append disabled input & button
                  $("#orderOutput").append(`<div class="input-group mb-3"><input disabled type="number" id="bidInput" class="form-control rounded" inputmode="numeric" placeholder="Gebot eingeben..."><div class="input-group-append"><span class="input-group-text" style="border-radius: 0!important;">.00 NHD</span><button type="button" id="orderProduct" class="btn btn-success" style="padding: .375rem .75rem!important;" disabled><s>Bieten</s></button></div></div>`);                
                  break;
              }
            }
          } else { // API-Key is empty
            // Open #setKeyModal
            $("#setKeyModal").modal("toggle");            
          }
        } else {  // API-Key is not set
          // Open #setKeyModal
          $("#setKeyModal").modal("toggle");
        }
      } else { // User is not logged in
        $("#auctionBar .row p").addClass("d-none"); // Make money information invisible
        if(offerType == "1") {
          // Append disabled button because user is not logged in
          $("#orderOutput").append(`<button id="orderProduct" class="btn btn-sm btn-success w-100" disabled><s>Kaufen</s></button>`);          
        } else { // Auction
          // Append disabled input & button because user is not logged in
          $("#orderOutput").append(`<div class="input-group mb-3"><input disabled type="number" id="bidInput" class="form-control rounded" inputmode="numeric" placeholder="Gebot eingeben..."><div class="input-group-append"><span class="input-group-text" style="border-radius: 0!important;">.00 NHD</span><button type="button" id="orderProduct" class="btn btn-success" style="padding: .375rem .75rem!important;" disabled><s>Bieten</s></button></div></div>`);
        }       
      }

      // Register EventListener
      // Toggle sign-in & sign-up modals
      $("#switchSignUp").on("click", () => {
        $("#signInModal").modal("toggle");
        $("#signUpModal").modal("toggle");
      });
      // Set API-Key
      $("#saveKeyBtn").on("click", () => {
        var apiKey = $("#apiKeyInput").val();
        ls.setItem("apiKey", apiKey);
        location.reload();
      });
      // Toggle images
      $(".image-toggle").on("click", function () {
        var imgURL = $(this).data("image");
        $("#thumbnailPlaceholder").attr("src", imgURL);
      });
      // Sign in
      $("#signInForm").submit((event) => {
        event.preventDefault();
        const username = $("#signInUsername").val();
        const password = $("#signInPassword").val();
        // Check if username exists
        const isRegistered = auction.isRegistered(username);
        if (isRegistered == true) {
          // User exists
          // Check if login data is correct
          const loginRes = auction.login(username, password);
          if(loginRes == true) {
            document.cookie = `username=${username}`; // Set cookie => login user in
            //toastr.success("Du wurdest angemeldet");
            location.reload(); // Reload website
          } else { // Password is incorrect
            toastr.error("Das Passwort ist falsch");
          }
        } else { // User doesn't exist
          toastr.error("Der Benutzer existiert nicht");
        }
      });
      // Sign up
      $("#signUpForm").submit((event) => {
        event.preventDefault();
        var username = $("#signUpUsername").val();
        var password = $("#signUpPassword").val();
        var email = $("#signUpEmail").val();
        // Check if username is already taken
        const isRegistered = auction.isRegistered(username);
        if(isRegistered == false) {
          // Username is free 2 take
          const registration = auction.register(username, password, email);
          if(registration == true) {
            //toastr.success("Du hast dich erfolgreich registriert");
            document.cookie= `username=${username}`; // Set cookie => login user in
            location.reload();
            //toastr.success("Du wurdest angemeldet");
            //auction.updateBar();
            //$("#signUpModal").modal("toggle");
            //$("#setKeyModal").modal("toggle");
          } else { // Something went wront
            toastr.error("Etwas ist schief gelaufen");
          }
        } else { // Username is already taken
          toastr.error("Der Benutzername ist bereits vergeben");
        }
      });
    </script>
  </body>
</html>
