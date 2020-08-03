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
      const auction = new Auction;
      const ls = localStorage;
      const rlapi = new ReallifeAPI;

      if (auction.isLoggedIn()) {
        const loginData = JSON.parse(new Cookie().get("dag_auction"));
        auction.updateDropdown(loginData.username);
        if (ls.hasOwnProperty("apiKey")) {
          const apiKey = ls.getItem("apiKey");
          if (apiKey != "") {
            const a3 = rlapi.getProfile(apiKey); // TODO Should return an object instead of an normal array
            document.querySelector("#playerBankAcc").innerText = `${parseInt(a3[4]).toLocaleString(undefined)} NHD`;
            document.querySelector("#playerCash").innerText = `${parseInt(a3[3]).toLocaleString(undefined)} NHD`;
          
            document.querySelector("#createOfferForm").addEventListener("submit", function (e) {
              e.preventDefault();
              const thumbnail = this.querySelector("#thumbnail").files[0];
              const images = this.querySelector("#images").files;
              const time = this.querySelector("#expire-time").value;
              const date = this.querySelector("#expire-date").value;
              const expiresAt = `${date} ${time}`;
              const desc = this.querySelector("#description").value;        
              const price = parseInt(this.querySelector("#price").value);
              const FormData = {
                owner: {
                  userId: loginData.userId,
                  username: loginData.username,
                  steamId: a3[15],
                },
                type: this.querySelector("#type").value,
                thumbnail: thumbnail != undefined ? thumbnail : null,
                images: images.length > 0 ? images : null,
                price: price != NaN ? price : 0,
                title: this.querySelector("#title").value,
                description: desc != "" ? desc : "<i>Keine Beschreibung</i>",
                expiresAt: new Date(expiresAt).getTime() / 1000,
              };

              // Do form validation
              if (FormData.thumbnail != null) {
                if (FormData.price != NaN) {
                  if (FormData.title != "") {
                    auction.createOffer(FormData)
                      .then((res) => {
                        //const offerId = res;
                        //console.log("[Auction]", offerId);
                        // TODO Display offer at #offer-output & remove "no offers found"-message if it's displayed
                        $("#createOfferModal").modal("hide");
                        toastr.success("Das Angebot wurde erstellt");
                      })
                      .catch((error) => {
                        console.error("[Auction]", error);
                      });
                  } else {
                    toastr.error("Bitte gib einen gültigen Titel an");
                  }
                } else {
                  toastr.error("Bitte gib einen gültigen Preis ein");
                }
              } else {
                toastr.error("Das Vorschaubild fehlt");
              }
            });
          } else {
            document.querySelectorAll("#auctionBar .row p").forEach((element) => {
              element.classList.add("d-none");
            });
          }
        } else {
          document.querySelectorAll("#auctionBar .row p").forEach((element) => {
            element.classList.add("d-none");
          });
        }

        // Edit profile
        auction.getUser(loginData.userId)
          .then((value) => {
            const profile = document.querySelector("#profile");
            profile.querySelector("#avatar").setAttribute("src", "https://files.dulliag.de/web/images/logo.jpg");
            profile.querySelector("#username").innerHTML = `@${value.username} <a id="edit" class="text-success" href="#"><i class="fas fa-pencil-alt"></i></a>`;
            profile.querySelector("#email").value = value.email;
            if (ls.hasOwnProperty("apiKey")) {
              const apiKey = ls.getItem("apiKey");
              if (apiKey != "" || apiKey != null) {
                profile.querySelector("#apiKey").value = apiKey;
              }
            }

            profile.querySelector("#edit").addEventListener("click", () => {
              profile.querySelector("#options").classList.remove("d-none");
              profile.querySelector("#password").readOnly = false;
              profile.querySelector("#email").readOnly = false;
              profile.querySelector("#apiKey").readOnly = false;
            });

            profile.querySelector("#cancel").addEventListener("click", () => {
              profile.querySelector("#options").classList.add("d-none");
              profile.querySelector("#password").readOnly = true;
              profile.querySelector("#email").readOnly = true;
              profile.querySelector("#apiKey").readOnly = true;
            });

            profile.querySelector("#save").addEventListener("click", () => {
              const apiKey = ls.getItem("apiKey");
              const newProfileData = {
                apiKey: profile.querySelector("#apiKey").value,
                email: profile.querySelector("#email").value,
                password: profile.querySelector("#password").value,
              };

              // Update API-Key
              if (apiKey != newProfileData.apiKey) {
                ls.removeItem("apiKey");
                ls.setItem("apiKey", newProfileData.apiKey);
                profile.querySelector("#apiKey").value = newProfileData.apiKey;
              }

              // Save changes
              auction
                .updateUser(loginData.userId, newProfileData.password, newProfileData.email)
                .catch((error) => {
                  console.error(error);
                  toastr.error("Die Änderungen konnten nicht gespeichert werden");
                });

              // Update website
              toastr.success("Die Änderungen wurden gespeichert");
              profile.querySelector("#email").value = newProfileData.email;
              profile.querySelector("#options").classList.add("d-none");
              profile.querySelector("#password").readOnly = true;
              profile.querySelector("#email").readOnly = true;
              profile.querySelector("#apiKey").readOnly = true;
            });
          });

        // Get active offers
        auction
          .getOffers()
          .then((data) => {
            var offerCount = 0;
            for (const key in data) {
              // Check if the offer is active and owned by the user
              const offer = data[key];

              /**
               * When we have an auction we wanna check if it's expired an someone did bid on that product
               * If this turns out to be true were gonna update the offer-bought-status
               */
              if (offer.offer.type == 2 && offer.offer.expired < new Date().getTime() / 1000 && offer.offer.bids != "null") {
                console.log("[Auction] Offer: ", key, " got bought");
                var temp = [], winner;
                const bids = offer.offer.bids;
                for (const key in bids) {
                  temp.push(bids[key]);
                }
                winner = temp.pop();
                auction
                  .buy(key, offer.owner.userId, winner.userId, winner.steam64Id)
                  .catch((error) => {
                    console.error(error);
                  });
              }

              if (offer.offer.expiresAt > new Date().getTime() / 1000 && offer.owner.userId == loginData.userId && offer.offer.buy.bought != true) {
                offerCount++;
                var thumbnail, type, title, description, price;
                
                thumbnail = offer.offer.images.thumbnail;

                type = offer.offer.type == 1
                  ? `<span class="badge badge-success p-2">Sofortkauf</span>`
                  : `<span class="badge badge-success p-2">Auktion</span>`;
                
                title = offer.offer.title;
                
                description = offer.offer.description;

                if (offer.offer.type == 1 || (offer.offer.type == 2 && offer.offer.bids == "null")) {
                  price = `${offer.offer.price.toLocaleString(undefined)} NHD`;
                } else {
                  var temp = [];
                  const bids = offer.offer.bids;
                  for (const key in bids) {
                    temp.push(bids[key]);
                 }
                  price = temp.pop();
                  price = `${price.amount.toLocaleString(undefined)} NHD`;
                }

                document.querySelector("#offerOutput").innerHTML += `<div class="col-md-4 px-0 px-md-3 mb-4"><div class="card shop-item border-0 bg-light" data-id="${key}"><div class="card-header p-0 position-relative border-0"><img class="card-img-top rounded" src="${thumbnail}" alt="Produktbild"><span class="badge badge-success position-absolute" style="top: 1rem; left: 1rem">${type}</span></div><div class="card-body"><h4 class="title p-0">${title}</h4><p class="text text-truncate p-0">${description}</p><p class="text font-weight-bold">Preis: <a href="https://dulliag.de/Auktionen/offer.php?offer=${key}" class="text-link">${price}</a></p></div></div></div>`;
              }
            }
            if (offerCount == 0) {
              document.querySelector("#offerOutput").innerHTML += `<div id="no-offers-found" class="w-100"><div class="bg-light rounded"><h4 class="title text-center font-weight-bold py-3">Keine Angebote gefunden</h4></div></div>`;
            }
          })
          .catch((error) => {
            console.error(error);
          });

        // Get sales-history
        auction
          .getOffers()
          .then((data) => {
            var offerCount = 0;
            for (const key in data) {
              // Check if the offer is active and owned by the user
              const offer = data[key];
              if (offer.owner.userId == loginData.userId) {
                offerCount++;
                var status, type, title, price, bid, buyer;

                if (offer.offer.buy.bought != true) {
                  if (offer.offer.expiresAt > new Date().getTime() / 1000) {
                    status = `<span class="badge badge-warning p-2">Aktiv</span>`;
                  } else {
                    status = `<span class="badge badge-danger p-2">Beendet</span>`;
                  }
                } else {
                  status = `<span class="badge badge-success p-2">Verkauft</span>`;
                }

                type = offer.offer.type == 1
                  ? `<span class="badge badge-success p-2">Sofortkauf</span>`
                  : `<span class="badge badge-success p-2">Auktion</span>`;

                title = `<a class="text-link" href="https://dulliag.de/Auktionen/offer.php?offer=${key}">${offer.offer.title}</a>`;

                if (offer.offer.type == 1 || (offer.offer.type == 2 && offer.offer.bids == "null")) {
                  price = `${offer.offer.price.toLocaleString(undefined)} NHD`;
                  bid = price;
                } else {
                  var temp = [];
                  const bids = offer.offer.bids;
                  for (const key in bids) {
                    temp.push(bids[key]);
                  }
                  temp = temp.pop();
                  price = `${temp.amount.toLocaleString(undefined)} NHD`;
                  bid = `<p class="text" data-toggle="tooltip" data-placement="top" title="PlayerID: ${temp.steam64Id}">${temp.amount.toLocaleString(undefined)} NHD</p>`;
                }

                buyer = offer.offer.buy.bought == true
                  ? `<p class="text" data-toggle="tooltip" title="PlayerID">${offer.offer.buy.buyerSteam64Id}</p>`
                  : `---`;

                document.querySelector("#sales").innerHTML += `<tr class="text-center"><td>${status}</td><td>${type}</td><td>${title}</td><td>${price}</td><td>${bid}</td><td>${buyer}</td></tr>`;
                $('[data-toggle="tooltip"]').tooltip();
              }
            }

            if (offerCount == 0) {
              document.querySelector("#sales").innerHTML = `<tr class="text-center font-weight-bold"><td colspan="6">Keine Angebote gefunden</td></tr>`;
            }
          })
          .catch((error) => {
            console.error(error);
            document.querySelector("#sales").innerHTML = `<tr class="text-center font-weight-bold"><td colspan="6">Die Angebote konnten nicht abgerufen werden</td></tr>`;
          });


        // Get buy-history
        auction
          .getOffers()
          .then((data) => {
            var offerCount = 0;
            var status, type, title, price, myBid;
            for (const key in data) {
              const offer = data[key];
              //console.log("[Auction] ", offer);

              type = offer.offer.type == 1 
                  ? `<span class="badge badge-success p-2">Sofortkauf</span>`
                  : `<span class="badge badge-success p-2">Auktion</span>`;

              title = `<a href="https://dulliag.de/Auktionen/offer.php?offer=${key}" class="text-link">${offer.offer.title}</a>`;

              const bids = offer.offer.bids;
              var allBids = [], userBids = [];
              if (offer.offer.type == 2 && bids != null) {
                for (const key in bids) {
                  const bid = bids[key];
                  allBids.push(bid);
                  if (bid.userId == loginData.userId) {
                    userBids.push(bid);
                  }
                }
              }
              if (offer.offer.buy.bought == true && offer.offer.buy.buyerId == loginData.userId) {
                offerCount++;
                status = `<span class="badge badge-success p-2">Gekauft</span>`;
                price = `${offer.offer.price.toLocaleString(undefined)} NHD`;
                myBid = price;
              } else if (offer.offer.type == 2 && userBids.length > 0) {
                offerCount++;
                if (offer.offer.expiresAt > new Date().getTime() / 1000) {
                  allBids = allBids.pop();
                  userBids = userBids.pop();
                  if (allBids.userId == loginData.userId) {
                    status = `<span class="badge badge-warning p-2">Höhstbietender</span>`;
                    price = `${allBids.amount.toLocaleString(undefined)} NHD`;
                    myBid = price;
                  } else {
                    status = `<span class="badge badge-danger p-2">Überboten</span>`;
                    price = `${allBids.amount.toLocaleString(undefined)} NHD`;
                    myBid = `${userBids.amount.toLocaleString(undefined)} NHD`;
                  }
                } else {
                  var winner = allBids.pop();
                  if (winner.userId == loginData.userId) {
                    status = `<span class="badge badge-success p-2">Gekauft</span>`;
                    price = `${winner.amount.toLocaleString(undefined)} NHD`;
                    myBid = price;
                  } else {
                    // In this case went wrong :|
                    status = `<span class="badge badge-danger p-2">ERROR</span>`;
                    price = `${winner.amount.toLocaleString(undefined)} NHD`;
                    userBids = userBids.pop();
                    myBid = `${userBids.amount.toLocaleString(undefined)} NHD`;
                  }
                }
              }

              document.querySelector("#purchases").innerHTML += `<tr class="text-center"><td>${status}</td><td>${type}</td><td>${title}</td><td>${price}</td><td>${myBid}</td></tr>`;
            }

            if (offerCount == 0) {
              document.querySelector("#purchases").innerHTML = `<tr class="text-center"><td colspan="5" class="font-weight-bold">Keine Angebote gefunden</td></tr>`;
            }
          })
          .catch((error) => {
            console.error(error);
            document.querySelector("#purchases").innerHTML = `<tr class="text-center font-weight-bold"><td colspan="5">Die Angebote konnten nicht abgerufen werden</td></tr>`;
          });
      } else {
        document.querySelectorAll("#auctionBar .row p").forEach((element) => {
          element.classList.add("d-none");
        });

        document.querySelector("#profile").classList.add("d-none");
        document.querySelector("#profileContent").classList.add("d-none");
        document.querySelector("#calloutLogin").classList.remove("d-none");
      }
      // Toggle sign-in & sign-up modals
      document.getElementById("switchSignUp").addEventListener("click", function () {
        $("#signInModal").modal("toggle");
        $("#signUpModal").modal("toggle");
      });
      // Set API-Key
      document.getElementById("saveKeyBtn").addEventListener("click", function () {
        ls.setItem("apiKey", document.querySelector("#apiKeyInput").value);
        location.reload();
      });
      // Sign in
      document.getElementById("signInForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const username = document.getElementById("signInUsername").value;
        const password = document.getElementById("signInPassword").value;
        auction.doLogin(username, password)
          .then((response) => {
            if(response) {
              toastr.success("Du hast dich angemeldet");
              // TODO Update page-content
              setTimeout(() => {
                location.reload();
              }, 1000);
            } else {
              toastr.error("Der Benutzername oder das Passwort sind falsch");
            }
          })
          .catch((error) => {
            alert(error);
          });
      });
      // Sign up
      document.getElementById("signUpForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const username = document.getElementById("signUpUsername").value;
        const password = document.getElementById("signUpPassword").value;
        const email = document.getElementById("signUpEmail").value;
        auction.doRegistration(username, password, email)
          .then((value) => {
            toastr.success("Du hast dich erfolgreich registriert");
            // TODO Update page-content
            setTimeout(() => {
              location.reload();
            }, 1000);
          })
          .catch((error) => {
            alert(error);
          });
      });
    </script>
  </body>
</html>
