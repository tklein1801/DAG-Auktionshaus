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
              Angebote
            </a>
          </li>
        </ul>
      </div><!-- ./breadcrumb-container -->

      <div class="section pb-0">
        <!--
          Filter
        -->
        <div id="filter-container" class="container">
          <div class="row px-0">
            <div class="form-group d-none d-lg-none mr-2">
              <select id="sortOffers" class="form-control">
                <option>Alle</option>
                <option>Sofortkauf</option>
                <option>Auktion</option>
              </select>
            </div><!-- ./form-control -->

            <div class="form-group d-none d-lg-none mr-2">
              <select id="sortBy" class="form-control">
                <option>Neuste</option>
                <option>Älteste</option>
                <option>Preis aufsteigend</option>
                <option>Preis absteigend</option>
              </select>
            </div><!-- ./form-control -->

            <div class="form-group d-none d-lg-none mr-2">
              <select id="sortCategory" class="form-control">
                <option>Dienstleistung</option>
                <option>Rohstoffe</option>
                <option>Kleidung</option>
                <option>Fahrzeuge</option>
                <option>Immobilien</option>
                <option>Waffen</option>
                <option>Attachments</option>
                <option>Bauteil</option>
                <option>Sonstiges</option>
              </select>
            </div><!-- ./form-group -->

            <div class="col px-0 d-none d-lg-none">
              <button class="btn btn-sm btn-success" style="border-radius: 8px;" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-sliders-h"></i> Filter
              </button>
            </div><!-- ./column -->

            <div class="form-group d-none ml-auto">
              <input type="text" id="searchOffer" class="form-control" placeholder="Angebot suchen...">
            </div><!-- ./form-group -->
          </div><!-- ./row -->
        </div><!-- ./filter-container -->

        <!--
          Offers
        -->
        <div class="container px-0">
          <div id="offerOutput" class="row">
            <!--
              Get offers from system.js
            -->
            <?php
              $itemSize = 3;
              $auc = new Auction;
              $auc->getOffers($itemSize);
            ?>
          </div><!-- ./row -->
        </div><!-- ./container -->
      </div><!-- ./support -->

      <?php require_once $comp_footer;?>
    </div><!-- ./page-container -->

    <?php
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
      const loginCookie = getCookie("username");

      // Check if user is logged in
      if(loginCookie != "" && loginCookie != " " && loginCookie != null) {
        // User is logged in
        // Update #auctionbar
        auction.updateBar();

        // Check if API-Key is set
        if(ls.hasOwnProperty("apiKey")) {
          var apiKey = ls.getItem("apiKey");
          // Check if API-Key is empty
          if(apiKey != "" || apiKey != " ") {
            // Set data for #auctionBar
            var pData = rlapi.getProfile(apiKey); // Returns array
            var cash = parseInt(pData[3]);
            var bankAcc = parseInt(pData[4]);
            $("#playerBankAcc").text(`${bankAcc.toLocaleString(undefined)} NHD`);
            $("#playerCash").text(`${cash.toLocaleString(undefined)} NHD`);
          } else { // API-Key is empty
            // Open modal
            $("#setKeyModal").modal("show");
          }
        } else { // API-Key is not set
          // Open modal
          $("#setKeyModal").modal("show");
        }
      } else { // User is not logged in
        $("#auctionBar .row p").addClass("d-none"); // Make money information invisible
      }

      // Register EvenListener
      // Redirect to offer
      $(".shop-item").on("click", function() {
        var offerID = $(this).data("id");
        $(location).attr("href", `https://dulliag.de/Auktionen/offer.php?id=${offerID}`)
      });
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
            toastr.success("Du wurdest angemeldet");
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
            toastr.success("Du hast dich erfolgreich registriert");
            document.cookie= `username=${username}`; // Set cookie => login user in
            toastr.success("Du wurdest angemeldet");
            auction.updateBar();
            $("#signUpModal").modal("toggle");
            $("#setKeyModal").modal("toggle");
          } else { // Something went wront
            toastr.error("Etwas ist schief gelaufen");
          }
        } else { // Username is already taken
          toastr.error("Der Benutzername ist bereits vergeben");
        }
      });
    </script>
    <script type="text/javascript">
      // Search an specific offer
      $('#searchOffer').keyup(() => {
        const itemAmount = $('.shop-item').length;
        var inputValue = $('#searchOffer').val();
        for (let i = 0; i < itemAmount; i++) {
          var item = document.getElementsByClassName('shop-item'); // Select  item
          var title = item[i].getElementsByTagName('h4')[0].textContent; // Select title from item
          console.log(title.toUpperCase().indexOf(inputValue.toUpperCase()));
          if(title.toUpperCase().indexOf(inputValue.toUpperCase()) > -1) {
            //item[i].style.display = '';
            $(item[i]).fadeIn();
          } else {
            //item[i].style.display = 'none';
            $(item[i]).fadeOut();
          }
        }
      });
    </script>
  </body>
</html>