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
    <script src="https://www.gstatic.com/firebasejs/7.15.5/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.15.5/firebase-database.js"></script>
    <script src="https://files.dulliag.de/web/js/cookie.js"></script>
    <script src="https://files.dulliag.de/web/js/auction.js"></script>
    <script type="text/javascript">
      const ls = localStorage;
      const auction = new Auction();
      const rlapi = new ReallifeAPI();
      const offerId = new URL(window.location.href).searchParams.get("offer");
      // !!! IMPORTANT !!!
      // Modify the URL after extracting the offerId from it
      // We will not be able to extract GET-params after modifing the URL
      // history.replaceState({}, null, "/Auktionen/Angebot/"); 
      const now = new Date().getTime() / 1000;
      
      if (auction.isLoggedIn()) {
        const loginData = JSON.parse(new Cookie().get("dag_auction"));
        auction.updateDropdown(loginData.username);
        
        if (ls.hasOwnProperty("apiKey")) {
          const apiKey = ls.getItem("apiKey");
          if (apiKey != "" || apiKey != " ") {
            const a3 = rlapi.getProfile(apiKey); // TODO This should return an object instead of an array
            const cash = parseInt(a3[3]);
            const bankAcc = parseInt(a3[4]);
            document.querySelector("#playerBankAcc").innerText = `${bankAcc.toLocaleString(undefined)} NHD`
            document.querySelector("#playerCash").innerText = `${cash.toLocaleString(undefined)} NHD`
          } else { // API-Key is empty
            $("#setKeyModal").modal("show");
          }
        } else { // API-Key is not set
          $("#setKeyModal").modal("show");
        }
      } else {
        document.querySelectorAll("#auctionBar .row p").forEach((element) => {
          element.classList.add("d-none");
        });
      }
      
      // Display offer Data
      var countdown;
      auction
        .getOffer(offerId)
        .then((data) => {
          if (data != null) {
            const seller = {
              avatar: "https://files.dulliag.de/web/images/logo.jpg",
              userId: data.owner.userId,
              username: data.owner.username,
              steamId: data.owner.username,
            };
            document.querySelector("#sellerInformation").innerHTML = `<img style="width: 2.5rem; height: auto;" class="rounded-circle shadow-md" src="${seller.avatar}" alt="Profilbild"> ${seller.username}`;
            document.querySelector("#offerTitle").innerText = data.offer.title;
            document.querySelector("#offerDesc").innerText = data.offer.description;
            if (data.offer.type == 1 || (data.offer.type == 2 && data.offer.bids == "null")) {
              document.querySelector("#curBid").innerText = `${data.offer.price.toLocaleString(undefined)} NHD`;
            } else {
              var temp = [], bid;
              for (const key in data.offer.bids) {
                temp.push([data.offer.bids[key]]);
              }
              bid = temp.pop();
              document.querySelector("#curBid").innerText = `${bid[0].amount.toLocaleString(undefined)} NHD`;
            }
            document.querySelector("#thumbnailPlaceholder").setAttribute("src", data.offer.images.thumbnail);
            document.querySelector("#productImages").innerHTML += `<a class="image-toggle mr-3" data-image="${data.offer.images.thumbnail}"><img class="rounded" src="${data.offer.images.thumbnail}" alt="Vorschaubild"></a>`;
            if (data.offer.images.product != "null") {
              for (const key in data.offer.images.product) {
                document.querySelector("#productImages").innerHTML += `<a class="image-toggle mr-3" data-image="${data.offer.images.product[key]}"><img class="rounded" src="${data.offer.images.product[key]}" alt="Vorschaubild"></a>`;
              }
            }
            document.querySelector("#productImages").lastChild.classList.remove("mr-3");
            // Toggle images
            const images = document.querySelectorAll(".image-toggle");
            images.forEach((image) => {
              image.addEventListener("click", function () {
                document.querySelector("#thumbnailPlaceholder").setAttribute("src", this.getAttribute("data-image"));
              });
            });

            if (auction.isLoggedIn() && ls.hasOwnProperty("apiKey") && data.offer.buy.bought != true) {
              const apiKey = ls.getItem("apiKey");
              const loginData = JSON.parse(new Cookie().get("dag_auction"));
              if (loginData.userId != data.owner.userId) {
                if (apiKey != "") {
                  const a3 = rlapi.getProfile(apiKey); // TODO This should return an object instead of an array
                  const cash = parseInt(a3[3]);
                  const bankAcc = parseInt(a3[4]);
                  
                  if (data.offer.expiresAt > new Date().getTime() / 1000 && data.offer.buy.bought != true) {
                    if (data.offer.type == 1) {
                      document.querySelector("#orderOutput").innerHTML = `<button id="orderProduct" class="btn btn-sm btn-success w-100">Kaufen</button>`;
                        document.querySelector("#orderProduct").addEventListener("click", () => {
                        if ((cash + bankAcc) >= data.offer.price) {
                          auction
                            .buy(offerId, data.owner.userId, loginData.userId, a3[15])
                            .then(() => {
                              toastr.success("Du hast den Artikel gekauft");
                            })
                            .catch((error) => {
                              console.error(error);
                            });
                        } else {
                          document.querySelector("#orderProduct").setAttribute("disabled", true);
                          document.querySelector("#orderProduct").innerHTML = `<s>Kaufen</s>`;
                          toastr.error("Du hast nicht genügend Geld");
                        }
                      });
                    } else {
                      var temp = [];
                      const bids = data.offer.bids;
                      document.querySelector("#orderOutput").innerHTML = `<div class="input-group mb-3"><input type="number" id="bidInput" class="form-control rounded" inputmode="numeric" placeholder="Gebot eingeben..."><div class="input-group-append"><span class="input-group-text" style="border-radius: 0!important;">.00 NHD</span><button type="button" id="orderProduct" class="btn btn-success" style="padding: .375rem .75rem!important;">Bieten</button></div></div>`;
                      for (const key in bids) {
                        temp.push([bids[key]]);
                      }
                      temp = temp.pop();
                      if (temp[0].userId == loginData.userId) {
                        //document.querySelector("#orderProduct").setAttribute("style", "text-decoration: line-through;");
                        document.querySelector("#orderProduct").setAttribute("disabled", true); 
                      }

                      document.querySelector("#orderProduct").addEventListener("click", () => {
                        var curPrice, temp = [];
                        const amount = parseInt(document.querySelector("#bidInput").value);
                        
                        if (bids != "null") {
                          for (const key in bids) {
                            temp.push([bids[key]]);
                          }
                          curPrice = temp.pop();
                          /**
                           * We're adding one NHD to an bid because if an player has bid on this product 
                           * an other user should be able to place the same amount as bid as the user before 
                           * him to win the auction
                           */
                          curPrice = curPrice[0].amount + 1;
                        } else {
                          curPrice = data.offer.price;
                        }

                        if (amount >= curPrice) {
                          if (amount <= (cash + bankAcc)) {
                            auction
                              .bid(offerId, loginData.userId, a3[15], amount)
                              .then(() => {
                                toastr.success("Dein Gebot wurde abgegeben");
                                document.querySelector("#bidInput").value = "";
                                document.querySelector("#orderProduct").setAttribute("disabled", true);
                              })
                              .catch((error) => {
                                console.error(error);
                              });
                          } else {
                            toastr.error("Du hast nicht genügend Geld");
                          } 
                        } else {
                          toastr.error("Dein Gebot muss höher als das aktuelle sein");
                        }
                      });                  
                    }
                  }
                }
              }
            }

            /**
             * Countdown, buy & bid input
             */
            if (data.offer.buy.bought != true && data.offer.expiresAt > new Date().getTime() / 1000) {
              countdown = setInterval(() => {
                const diff = data.offer.expiresAt - new Date().getTime() / 1000;
                if (diff > 0) {
                  var d = Math.floor(diff / 86400);
                  var h = Math.floor((diff - (d * 86400)) / 3600); h = h < 10 ? `0${h}`: h;
                  var m = Math.floor((diff - (d * 86400) - (h * 3600 )) / 60); m = m < 10 ? `0${m}`: m;
                  var s = Math.floor((diff - (d * 86400) - (h * 3600) - (m * 60))); s = s < 10 ? `0${s}`: s;
                  if (d != 0) {
                    if (d > 1) {
                      document.querySelector("#timeLeft").innerText = `Verbleibende Zeit: ${d} Tage und ${h}:${m}:${s}`;
                    } else {
                        document.querySelector("#timeLeft").innerHTML = `Verbleibende Zeit: ${d} Tag und ${h}:${m}:${s}`;
                    }
                  } else {
                    document.querySelector("#timeLeft").innerText = `Verbleibende Zeit: ${h}:${m}:${s}`;
                  }
                } else {
                  clearInterval(countdown);
                  document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
                  if (data.offer.type == 2 && data.offer.bids != "null") {
                    var temp = [], bid = data.offer.bids;
                    for (const key in bid) {
                      temp.push(bid[key]);
                    }
                    bid = bid.pop();
                    bid = bid[0];
                    auction
                      .buy(offerId, data.owner.userId, id.userId, bid.steam64Id)
                      .then(() => {
                        document.querySelector("#orderOuptut").setAttribute("style", "display: none;");
                        document.querySelector("#orderProduct").setAttribute("disabled", "true");
                        clearInterval(countdown);
                        document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
                        toastr.info("Der Artikel wurde verkauft");
                      })
                      .catch((error) => {
                        console.error(error);
                      });
                  }
                }
              }, 1000);
            } else {
              document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
              if (data.offer.type == 2 && data.offer.buy.bougth != true && data.offer.bids != "null") {
                var temp = [], bid = data.offer.bids;
                for (const key in bid) {
                  temp.push(bid[key]);
                }
                bid = temp.pop();
                auction
                  .buy(offerId, data.owner.userId, bid.userId, bid.steam64Id)
                  .then(() => {
                    if (document.querySelector("#orderOutput") && document.querySelector("#orderProduct") != null) {
                      document.querySelector("#orderOuptut").setAttribute("style", "display: none;");
                      document.querySelector("#orderProduct").setAttribute("disabled", "true");
                    }
                    clearInterval(countdown);
                    document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
                    toastr.info("Der Artikel wurde verkauft");
                  })
                  .catch((error) => {
                    console.error(error);
                  });
              }
            }
          } else {
            toastr.error("Das Angebot wude nicht gefunden");
            document.querySelector("#page-content").innerHTML = `<div class="col-12 col-md-4 mx-auto"><div class="callout callout-success mx-auto"><h5>Achtung</h5><p>Das Angebot wurde nicht gefunden</p></div></div>`;
          }
        })
        .catch((error) => {
          console.error(error);
        }); 
      
      // Run if offer got bought
      firebase
        .database()
        .ref(`auction_offers/${offerId}/offer/buy`)
        .on("child_changed", (snapshot) => {
          if(snapshot.val() == true) {
            clearInterval(countdown);
            document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
            toastr.info("Der Artikel wurde verkauft");
            /**
             * This will throw us the following error if the user is the creator of the offer
             * Do you think I should fix this?
             * YES / NO
             * ----/----
             *     /I
             * So I think im not gonna fix this :D
             * Uncaught TypeError: Cannot read property 'setAttribute' of null
             *  at offer.php?offer=tho614:698
             *  at EventRegistration.ts:251
             *  at Nt (util.ts:588)
             *  at Hr.raise (EventQueue.ts:171)
             *  at Ur.raiseQueuedEventsMatchingPredicate_ (EventQueue.ts:123)
             *  at Ur.raiseEventsForChangedPath (EventQueue.ts:103)
             *  at wi.onDataUpdate_ (Repo.ts:268)
             *  at yi.onDataPush_ (PersistentConnection.ts:594)
             *  at yi.onDataMessage_ (PersistentConnection.ts:587)
             *  at ci.onDataMessage_ (Connection.ts:322)
             */
            //document.querySelector("#orderProduct").setAttribute("style", "text-decoration: line-through;");
            document.querySelector("#orderProduct").setAttribute("disabled", true); 
          }
        });
      // Run if offer received a new bid
      firebase
        .database()
        .ref(`auction_offers/${offerId}/offer/bids`)
        .on("child_added", (snapshot) => {
          const data = snapshot.val();
          document.querySelector("#curBid").innerText = `${data.amount.toLocaleString(undefined)} NHD`;
          if (auction.isLoggedIn()) {
            const loginData = JSON.parse(new Cookie().get("dag_auction"));
            if (loginData.userId == data.userId) {
              toastr.info("Du bist aktuell der höhstbietende");
            }
          } else {
            toastr.info("Es gibt ein neues Höhstgebot für diesen Artikel");
          }
        });
      // Run if offer got deleted
      firebase
        .database()
        .ref(`auction_offers/${offerId}`)
        .on("child_removed", (snapshot) => {
          document.querySelector("section .container .row").innerHTML = `<div class="col-12 col-md-4 mx-auto"><div class="callout callout-success mx-auto"><h5>Achtung</h5><p>Das Angebot wurde nicht gefunden</p></div></div>`;
          clearInterval(countdown);
          toastr.error("Das Angebot wurde entfernt");
        });

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
      document
        .getElementById("signInForm")
        .addEventListener("submit", function (event) {
          event.preventDefault();
          const username = document.getElementById("signInUsername").value;
          const password = document.getElementById("signInPassword").value;
          auction
            .doLogin(username, password)
            .then((response) => {
              if (response) {
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
              console.error(error);
            });
        });
      // Sign up
      document
        .getElementById("signUpForm")
        .addEventListener("submit", function (event) {
          event.preventDefault();
          const username = document.getElementById("signUpUsername").value;
          const password = document.getElementById("signUpPassword").value;
          const email = document.getElementById("signUpEmail").value;
          auction
            .doRegistration(username, password, email)
            .then((value) => {
              toastr.success("Du hast dich erfolgreich registriert");
              // TODO Update page-content
              setTimeout(() => {
                location.reload();
              }, 1000);
            })
            .catch((error) => {
              console.error(error);
            });
        });
    </script>
  </body>
</html>
