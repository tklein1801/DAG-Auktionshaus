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
    <style media="screen">
      #profile input:read-only {
        border: 0px!important;
        background-color: transparent!important;
        border-radius: 0px!important;
      }

      #profile input {
        border-radius: 0px!important;
        border-top: 0px!important;
        border-left: 0px!important;
        border-right: 0px!important;
        border-bottom: 2px solid #ededed!important;
        background-color: transparent!important;
      }
    </style>

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
            <a class="text-center mr-0" href="https://dulliag.de/Auktionen/Profil/">
              <i class="fas fa-user-circle"></i>
              Profil
            </a>
          </li>
        </ul>
      </div><!-- ./breadcrumb-container -->

      <div class="section">


        <div class="container px-0">
          <div class="row">
            <div id="calloutLogin" class="callout callout-success mx-auto d-none">
              <h5>Achtung</h5>
              <p>Bitte melde dich <a class="text-link" href="#" data-toggle="modal" data-target="#signInModal">hier</a> an um dein Profil einsehen zu können.</p>
            </div><!-- ./callout -->

            <div id="profile" class="col-md-4 col-xl-3 mb-3">
              <div class="bg-light w-100 p-4 rounded">
                <img id="profileImage" class="w-50 mx-auto mb-3 rounded-circle" src="<?php echo $dagLogo; ?>" alt="Profilbild" style="margin-left: 25%!important;">

                <p id="profileUsername" class="text font-weight-bold text-center mb-2">
                  @Benutzername
                </p>

                <div id="btnOutput" class="btn-group mb-3 w-100 d-none">
                  <button type="button" id="cancelUpdate" class="btn btn-sm btn-outline-secondary w-50">Abbrechen</button>
                  <button type="button" id="saveUpdate" class="btn btn-sm btn-success w-50">Speichern</button>
                </div><!-- ./btn-group -->

                <div class="row">
                  <div class="w-100 form-group row">
                    <label class="col-form-label p-0">Passwort</label>
                    <input type="text" name="profilePassword" id="profilePassword" class="form-control form-control-sm p-0" placeholder="Passwort unsichtbar" readonly>
                  </div><!-- ./form-group -->

                  <div class="w-100 form-group row">
                    <label class="col-form-label p-0">E-Mail</label>
                    <input type="email" name="profileEmail" id="profileEmail" class="form-control form-control-sm p-0" value="Lädt..." readonly>
                  </div><!-- ./form-group -->

                  <div class="w-100 form-group row">
                    <label class="col-form-label p-0">API-Key</label>
                    <input type="text" name="profileApiKey" id="profileApiKey" class="form-control form-control-sm p-0" value="Lädt..." readonly>
                  </div><!-- ./form-group -->
                </div><!-- ./row -->
              </div>
            </div><!-- ./profile -->

            <div id="profileContent" class="col-md-8 col-xl-9">
              <div>
                <div class="header mb-3 bg-light rounded">
                  <nav class="nav nav-pills nav-justified">
                    <a href="#tab-offers" id="nav-offers" class="nav-item nav-link active" data-toggle="tab">
                      Auktionen
                    </a>
                    <a href="#tab-bids" id="nav-bids" class="nav-item nav-link" data-toggle="tab">
                      Angebote
                    </a>
                    <a href="#tab-history" id="nav-history" class="nav-item nav-link mr-2" data-toggle="tab">
                      Verlauf
                    </a>
                    <a href="#tab-messages" id="nav-messages" class="nav-item nav-link d-none" data-toggle="tab">
                      Nachrichten
                    </a>
                  </nav><!-- ./nav-pills -->

                  <div class="btn-group w-100 px-3 pb-3">
                    <button type="button" class="btn btn-sm btn-success font-weight-bol
                    d rounded" data-toggle="modal" data-target="#createOfferModal">
                      Angebot erstellen
                    </button>
                  </div>
                </div><!-- ./header -->

                <div class="body">
                  <div class="tab-content">
                    <div id="tab-offers" class="tab-pane fade show active" role="tabpanel">
                      <div id="offerOutput" class="row">                        
                        <!--
                          Get content from js
                        -->
                      </div><!-- ./row -->
                    </div><!-- ./tab-offers -->
                    <div id="tab-bids" class="tab-pane fade" role="tabpanel">
                      <div class="table-responsive bg-light rounded p-0">
                        <table class="table table-borderless nowrap">
                          <thead>
                            <tr class="text-center">
                              <th>Status</th>
                              <th>Art</th>
                              <th>Produkt</th>
                              <th>Preis</th>
                              <th>Aktuelles Gebot</th>
                              <th>Verkauft an</th>
                            </tr>
                          </thead>
                          <tbody id="myOfferOutput">
                            <!-- Get data with js -->
                          </tbody>
                        </table>
                      </div><!-- ./table-responsive -->
                    </div><!-- ./tab-bids -->
                    <div id="tab-history" class="tab-pane fade" role="tabpanel">
                      <div class="table-responsive bg-light rounded p-0">
                        <table class="table table-borderless nowrap">
                          <thead>
                            <tr class="text-center">
                              <th>Status</th>
                              <th>Art</th>
                              <th>Produkt</th>
                              <th>Preis</th>
                              <th>Mein Gebot</th>
                            </tr>
                          </thead>
                          <tbody id="bidOutput">
                            <!-- Get data with js -->
                          </tbody>
                        </table>
                      </div><!-- ./table-responsive -->
                    </div><!-- ./tab-history -->
                    <div id="tab-messages" class="tab-pane fade" role="tabpanel">
                      <h4 class="title text-center">Nachrichten</h4>
                    </div><!-- ./tab-messages -->
                  </div><!-- ./tab-content -->
                </div><!-- ./body -->
              </div><!-- ./p-4 -->
            </div><!-- ./profileContent -->
          </div><!-- ./row -->
        </div><!-- ./container -->
      </div><!-- ./support -->

      <?php require_once $comp_footer;?>
    </div><!-- ./page-container -->

    <?php
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/createOffer.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/apiKey.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/signInModal.php';
      require_once '/var/www/vhosts/dulliag.de/files.dulliag.de/web/php/auction/modal/signUpModal.php';
    ?>

    <?php require_once $comp_script; ?>
    <script>
      activeMenuItem("auctionLink");
    </script><!-- ./navbarJS -->

    <!-- This page script -->
    <script src="https://files.dulliag.de/web/js/auction.js"></script>
    <script type="text/javascript">
      const user = new Auction;
      const ls = localStorage;
      const rlapi = new ReallifeAPI;
      const loginCookie = getCookie("username"); // Returns cookie value

      // Check if user is logged
      if(loginCookie != "" && loginCookie != " " && loginCookie != null) {
        const userData = user.getProfile(loginCookie);
        // User is logged in
        user.updateBar();
        // Set profile data
        $("#profileEmail").val(userData[3]);
        // Check if API-Key is set
        if(ls.hasOwnProperty("apiKey")) {
          const apiKey = ls.getItem("apiKey");
          if(apiKey == "" || apiKey == " ") {
            // API-Key is not set
            $("#setKeyModal").modal("show");
            $("#saveKeyBtn").on("click", () => {
              var inputVal = $("#apiKeyInput").val();
              ls.setItem("apiKey", inputVal);
              location.reload();
            });
          } else {
            var profileData = rlapi.getProfile(apiKey);
            var steamID = profileData[15];
            var cash = parseInt(profileData[3]);
            cash = cash.toLocaleString(undefined);
            var bankAcc = parseInt(profileData[4]);
            bankAcc = bankAcc.toLocaleString(undefined);
            $("#playerBankAcc").text(`${bankAcc} NHD`);
            $("#playerCash").text(`${cash} NHD`);
            // Set profile data
            $("#profileUsername").html(`@${loginCookie} <a id="editProfile" class="text-success" href="#"><i class="fas fa-pencil-alt"></i></a>`);
            $("#profileApiKey").val(apiKey);

            // Create new offer
            $("#createOfferForm").submit((event) => {
              event.preventDefault();
              let error = false;
              const fData = new FormData();
              var offerType = $("#createOfferType").val();
              var prodPrice = $("#createOfferPrice").val();
              var prodName = $("#createOfferTitle").val();
              var prodDesc = $("#createOfferDesc").val();
              var prodDate = $("#createOfferDate").val();
              var prodTime = $("#createOfferTime").val();
              var endTime = Date.parse(`${prodDate} ${prodTime}`);
              endTime = endTime / 1000; // Milliseconds => Seconds
              let hasThumbnail = false;
              var thumbnail = $("#createOfferThumbnail")[0]; // Get file
              let hasProdImages = false;
              var productImages = $("#createOfferImages")[0]; // Get files

              // Add input value to form-data
              fData.append("steam64Id", steamID);
              fData.append("displayname", loginCookie);
              fData.append("offerType", offerType);
              fData.append("offerPrice", prodPrice);
              fData.append("offerName", prodName);
              fData.append("offerDesc", prodDesc);
              fData.append("offerExpires", endTime);
              if(thumbnail.files.length > 0) { // min. 1 image selected
                hasThumbnail = true;
                fData.append("hasThumbnail", hasThumbnail);
                fData.append("thumbnail", thumbnail.files[0]);
              } else { // no image selected
                error = true;
                fData.append("hasThumbnail", hasThumbnail);
                toastr.error("Du brauchst ein Vorschaubild");
              }
              if(productImages.files.length > 0) { // min. 1 image selected
                hasProdImages = true;
                fData.append("hasProductImages", hasProdImages);
                for (const img of productImages.files) {
                  fData.append("productImages[]", img);
                }
              } else { // no image selected
                fData.append("hasProductImages", hasProdImages);
              }

              // Create offer
              if(error != true) {
                $.ajax({
                  url: "https://api.dulliag.de/auction/v2/createOffer.php",
                  async: false,
                  processData: false,
                  contentType: false,
                  data: fData,
                  method: "post",
                  success: (response) => {
                    console.log(response);
                    if(response  == "1") {
                      $("#offerOutput .shop-item").remove();
                      proOffers(steamID);
                      $("#createOfferModal").modal("hide");
                      toastr.success("Dein Angebot wurde erstellt");
                    } else {
                      toastr.error("Etwas ist schief gelaufen");
                    }
                  }, error: (response) => {
                    console.log(response);
                  }
                });
              }
            });

            // Get profile offers
            $.ajax({
              url: "https://api.dulliag.de/auction/v2/getProfileOffers.php",
              async: false,
              data: {
                steam64Id: steamID
              },
              method: 'get',
              success: (response) => {
                $("#offerOutput").append(response);
              }, error: (response) => {
                console.log(response);
              }
            });
            function proOffers(steamID) {
              $.ajax({
                url: "https://api.dulliag.de/auction/v2/getProfileOffers.php",
                async: false,
                data: {
                  steam64Id: steamID
                },
                method: 'get',
                success: (response) => {
                  $("#offerOutput").append(response);
                }, error: (response) => {
                  console.log(response);
                }
              });
            }

            $(".shop-item").on("click", function() {
              var offerID = $(this).data("id");
              $(location).attr("href", `https://dulliag.de/Auktionen/offer.php?id=${offerID}`);
            });

            $.ajax({
              url: "https://files.dulliag.de/web/php/auction/a.php",
              async: false,
              data: {
                steamID: steamID
              },
              method: 'get',
              success: (response) => {
                $("#bidOutput").append(response);
              }, error: (response) => {
                console.log(response);
              }
            });

            $.ajax({
              url: "https://files.dulliag.de/web/php/auction/b.php",
              async: false,
              data: {
                steamID: steamID
              },
              method: 'get',
              success: (response) => {
                $("#myOfferOutput").append(response);
              }, error: (response) => {
                console.log(response);
              }
            });
          }
        } else {
          $("#profileApiKey").val("Nicht gesetzt");
          $("#setKeyModal").modal("show");
          $("#saveKeyBtn").on("click", () => {
            var inputVal = $("#apiKeyInput").val();
            ls.setItem("apiKey", inputVal);
            location.reload();
          });
        }
      } else {
        $('#calloutLogin').removeClass('d-none');
        $('#profile').addClass('d-none');
        $('#profileContent').addClass('d-none');
      }

      // Toggle modals
      $('#switchSignUp').on('click', () => {
        $('#signInModal').modal('hide'); // Close sign in modal
        $('#signUpModal').modal('show'); // Open sign up modal
      });

      // Sign in
      $('#signInForm').submit((event) => {
        event.preventDefault();
        var username = $('#signInUsername').val();
        var password = $('#signInPassword').val();
        // Check if user exists
        var isRegistered = user.isRegistered(username);
        if(isRegistered == true) {
          var login = user.login(username, password);
          // Check if login data is correct
          if(login == true) {
            // Create cookie
            document.cookie = `username=${username}`;
            toastr.success("Du wurdest angemeldet");
            user.updateBar();
            location.reload();
            $("#signInModal").modal("hide");
            // Check if API-Key is set
            if(ls.hasOwnProperty("apiKey")) {
              // API-Key is set
              const apiKey = ls.getItem("apiKey");
              if(apiKey == "" || apiKey == " ") {
                // API-Key is not set
                $("#setKeyModal").modal("show");
                $("#saveKeyBtn").on("click", () => {
                  var inputVal = $("#apiKeyInput").val();
                  ls.setItem("apiKey", inputVal);
                  location.reload();
                });
              } else {
                var profileData = rlapi.getProfile(apiKey);
                var cash = parseInt(profileData[3]);
                cash = cash.toLocaleString(undefined);
                var bankAcc = parseInt(profileData[4]);
                bankAcc = bankAcc.toLocaleString(undefined);
                $("#playerBankAcc").text(`${bankAcc} NHD`);
                $("#playerCash").text(`${cash} NHD`);
                // Set profile data
                $("#profileUsername").html(`@${loginCookie} <a id="editProfile" class="text-success" href="#"><i class="fas fa-pencil-alt"></i></a>`);
                $("#profileApiKey").val(apiKey);
              }
            } else {
              // API-Key is not set
              $("#setKeyModal").modal("show");
              $("#saveKeyBtn").on("click", () => {
                var inputVal = $("#apiKeyInput").val();
                ls.setItem("apiKey", inputVal);
                location.reload();
              });
            }
          } else {
            toastr.error("Deine Anmeldedaten sind falsch");
          }
        } else {
          toastr.error("Der Benutzer existiert nicht");
        }
      });

      // Sign up
      $("#signUpForm").submit((event) => {
        event.preventDefault();
        var username = $("#signUpUsername").val();
        var password = $("#signUpPassword").val();
        var email = $("#signUpEmail").val();
        var isRegistered = user.isRegistered(username);
        if(isRegistered == false) {
          var registration = user.register(username, password, email);
          if(registration == true) {
            user.updateBar();
            toastr.success("Du hast dich erfolgreich registriert");
            toastr.success("Du wurdest angemeldet")
            document.cookie = `username=${username}`;
            $("#signUpModal").modal("hide");
            if(ls.hasOwnProperty("apiKey")) {
              // API-Key is set
              const apiKey = ls.getItem("apiKey");
              if(apiKey == " ") {
                // API-Key is not set
                $("#setKeyModal").modal("show");
                $("#saveKeyBtn").on("click", () => {
                  var inputVal = $("#apiKeyInput").val();
                  ls.setItem("apiKey", inputVal);
                  location.reload();
                });
              } else {
                var profileData = rlapi.getProfile(apiKey);
                var cash = parseInt(profileData[3]);
                cash = cash.toLocaleString(undefined);
                var bankAcc = parseInt(profileData[4]);
                bankAcc = bankAcc.toLocaleString(undefined);
                $("#playerBankAcc").text(`${bankAcc} NHD`);
                $("#playerCash").text(`${cash} NHD`);
              }
            } else {
              // API-Key is not set
              $("#setKeyModal").modal("show");
              $("#saveKeyBtn").on("click", () => {
                var inputVal = $("#apiKeyInput").val();
                ls.setItem("apiKey", inputVal);
                location.reload();
              });
            }
          } else {
            toastr.error("Das Registrieren ist fehlgeschlagen");
          }
        } else {
          toastr.error("Der Benutzer existiert bereits");
        }
      });

      // Edit profile
      $("#editProfile").on("click", () => {
        $("#btnOutput").removeClass("d-none");
        $("#profilePassword").attr("readonly", false);
        $("#profileEmail").attr("readonly", false);
        $("#profileApiKey").attr("readonly", false);
      });

      $("#cancelUpdate").on("click", () => {
        $("#btnOutput").addClass("d-none");
        $("#profilePassword").attr("readonly", true);
        $("#profileEmail").attr("readonly", true);
        $("#profileApiKey").attr("readonly", true);
      });

      $("#saveUpdate").on("click", () => {
        var curKey = ls.getItem("apiKey");
        var newPw = $("#profilePassword").val();
        var newEmail = $("#profileEmail").val();
        var newKey = $("#profileApiKey").val();
        if (curKey != newKey) {
          ls.removeItem("apiKey");
          ls.setItem("apiKey", newKey);
        }
        var update = user.updateProfile(loginCookie, newPw, newEmail);
        if(update == true) {
          toastr.success("Dein Profil wurde gespeichert");
          // Updaet input value
          $("#profilePassword").val(newPw);
          $("#profileEmail").val(newEmail);
          $("#apiKeyInput").val(newKey);
          // Remove buttons & readonly => true
          $("#btnOutput").addClass("d-none");
          $("#profilePassword").attr("readonly", true);
          $("#profileEmail").attr("readonly", true);
          $("#profileApiKey").attr("readonly", true);
        } else {
          toastr.error("Etwas ist schiefgelaufen");
        }
      });
    </script>
  </body>
</html>
