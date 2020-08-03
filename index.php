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
      const auction = new Auction();
      const rlapi = new ReallifeAPI;

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

      // Get offers from firebase
      const offerOutput = document.querySelector("#offer-output");
      var offerAmount = 0, activeOffers = 0, offerList = [], tempKeys = [];
      auction
        .getOffers()
        .then((offer) => {
          for(const key in offer) {
            offerAmount += 1;
            tempKeys.push(key);
            if(offer[key].offer.buy.bought != true && offer[key].offer.expiresAt > new Date().getTime() / 1000) {
              activeOffers++;
              var price;
              if (offer[key].offer.type == 1 || (offer[key].offer.type == 2 && offer[key].offer.bids == "null")) {
                price = offer[key].offer.price.toLocaleString(undefined);
              } else {
                var temp = [], bid;
                for (const k in offer[key].offer.bids) {
                  temp.push([offer[key].offer.bids[k]]);
                }
                bid = temp.pop();
                price = bid[0].amount.toLocaleString(undefined);
              }

              offerOutput.innerHTML += `<div id="offer-${key}" class="col-md-3 mb-4"><div class="card offer border-0 bg-light" data-offer="${key}"><div class="card-header p-0 position-relative border-0"><img id="thumbnail" class="card-img-top rounded" src="${offer[key].offer.images.thumbnail}" alt="Vorschaubild"><span class="badge badge-success p-2 position-absolute" style="top: 1rem; left: 1rem">${offer[key].offer.type == 1 ? "Sofortkauf" : "Versteigerung"}</span></div><div class="card-body"><h4 id="title" class="title p-0">${offer[key].offer.title}</h4><p id="desc" class="text text-truncate p-0">${offer[key].offer.description}</p><p class="text font-weight-bold">Preis: <a href="https://dulliag.de/Auktionen/offer.php?offer=${key}" id="price" class="text-link">${price.toLocaleString(undefined)} NHD</a></p></div></div></div>`;
            } else if(offer[key].offer.type == 2 && offer[key].offer.bids != "null" && offer[key].offer.buy.bought != true) {
              activeOffers++;
              var temp = [], bid = offer[key].offer.bids;
              for (const key in bid) {
                temp.push(bid[key]);
              }
              bid = temp.pop();
              auction
                .buy(key, offer[key].owner.userId, bid.userId, bid.steam64Id)
                .then(() => {
                  console.log(`[Auction] ${key} was sold!`);
                  //document.querySelector("#orderOuptut").setAttribute("style", "display: none;");
                  //document.querySelector("#orderProduct").setAttribute("disabled", "true");
                  //clearInterval(countdown);
                  //document.querySelector("#timeLeft").innerText = "Das Angebot ist nicht mehr verfügbar";
                  //toastr.info("Der Artikel wurde verkauft");
                })
                .catch((error) => {
                  console.error(error);
                });
            }
          }
          if (activeOffers == 0) {
            offerOutput.innerHTML = `<div class="col mb-4"><div class="bg-light rounded"><h4 class="title text-center font-weight-bold py-3">Keine Angebote gefunden</h4></div></div>`;
          }

          const offers = offerOutput.querySelectorAll(".offer");
          offers.forEach(offer => {
            offer.addEventListener("click", function () {
              const offerId = this.getAttribute("data-offer");
              location.href = `https://dulliag.de/Auktionen/offer.php?offer=${offerId}`;
            });
          });
        })
        .then(() => {
          // Run if an ne woffer was added
          firebase
            .database()
            .ref("auction_offers")
            .on("child_added", (snapshot) => {
              // Check if there is realy a new offer
              const offer = document.querySelector(`#offer-${snapshot.key}`);
              if (typeof(offer) != "undefined" && offer == null) {
                const offerId = snapshot.key;
                const offerData = snapshot.val();
                // FIXME New offers will be shown at the end but they should be first
                if(offerData.offer.buy.bought != true && offerData.offer.expiresAt > new Date().getTime() / 1000) {
                  offerOutput.innerHTML += `<div id="offer-${offerId}" class="col-md-3 mb-4"><div class="card offer border-0 bg-light" data-offer="${offerId}"><div class="card-header p-0 position-relative border-0"><img id="thumbnail" class="card-img-top rounded" src="${offerData.offer.images.thumbnail}" alt="Vorschaubild"><span class="badge badge-success p-2 position-absolute" style="top: 1rem; left: 1rem">${offerData.offer.type == 1 ? "Sofortkauf" : "Versteigerung"}</span></div><div class="card-body"><h4 id="title" class="title p-0">${offerData.offer.title}</h4><p id="desc" class="text text-truncate p-0">${offerData.offer.description}</p><p class="text font-weight-bold">Preis: <a href="https://dulliag.de/Auktionen/offer.php?offer=${offerId}" id="price" class="text-link">${offerData.offer.price.toLocaleString(undefined)} NHD</a></p></div></div></div>`;
                }
              }
            });
        })
        .catch((error) => {
          console.error(error)
        });
      // Check if an offer has removed
      firebase
        .database()
        .ref("auction_offers")
        .on("child_removed", (snapshot) => {
          offerOutput.querySelector(`#offer-${snapshot.key}`).setAttribute("style", "display: none;");
        });
      // Check if an offer has changed
      firebase
        .database()
        .ref("auction_offers")
        .on("child_changed", (snapshot) => {
          const offerData = snapshot.val();
          /**
          * Check if the offer was bought
          * If the offer was bought it should get removed from the website
          */
          const offer = document.querySelector(`#offer-${snapshot.key}`);
          if (offerData.offer.buy.bought != true && offer != null) {
            offer.querySelector("#thumbnail").setAttribute("src", offerData.offer.images.thumbnail);
            offer.querySelector("#title").innerText = offerData.offer.title;
            offer.querySelector("#desc").innerText = offerData.offer.description;
            if (offerData.offer.type == 1 || (offerData.offer.type == 2 && offerData.offer.bids == "null")) {
              offer.querySelector("#price").innerText = `${offerData.offer.price.toLocaleString(undefined)} NHD`;
            } else {
              var temp = [], bid;
              for (const key in offerData.offer.bids) {
                temp.push([offerData.offer.bids[key]]);
              }
              bid = temp.pop();
              document.querySelector("#price").innerText = `${bid[0].amount.toLocaleString(undefined)} NHD`;
            }
          } else {
            if (offer != "null") {
              offerOutput.querySelector(`#offer-${snapshot.key}`).setAttribute("style", "display: none;");              
            }
          }
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
            console.error(error);
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
            console.error(error);
          });
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
