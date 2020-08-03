class Auction {
  constructor() {
    this.api = "https://api.dulliag.de/auction/";
    this.firebaseConfig = {
      apiKey: "ENTER",
      authDomain: "ENTER",
      databaseURL: "ENTER",
      projectId: "ENTER",
      storageBucket: "ENTER",
      messagingSenderId: "ENTER",
      appId: "ENTER",
      measurementId: "ENTER",
    };
    firebase.initializeApp(this.firebaseConfig);
    this.fb = firebase.database();
  }

  /**
   * Create a new offer => Promise
   * @deprecated Use createOffer() instead of this function
   * @param {object} offer Includes type, title, description, price, thumbnail, product[], expiresAt
   * @param {object} owner Includes userId, username, steam64Id
   */
  addOffer(offer, owner) {
    return new Promise((res, rej) => {
      const offerId = `${owner.userId}${offer.title.substring(0, 4).toLowerCase()}${
        new Date().getMonth() + 1
      }${new Date().getDate()}${new Date().getSeconds()}`;
      const offer = {
        offer: {
          type: offer.type,
          title: offer.title,
          description: offer.description,
          price: offer.price,
          buy: {
            bought: false,
            buyerId: "null",
            buyerSteam64Id: "null",
          },
          images: {
            thumbnail: offer.thumbnail,
            product: offer.product,
          },
          expiresAt: offer.expiresAt,
        },
        owner: {
          userId: owner.userId,
          username: owner.username,
          steam64Id: owner.steam64Id,
        },
      };

      firebase
        .database()
        .ref(`auction_offers/${offerId}`)
        .set(offer)
        .catch((error) => {
          rej(error);
        });
      res(offerId);
    });
  }

  /**
   * Get a list of all active offers
   */
  getOffers() {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref("auction_offers")
        .on(
          "value",
          (response) => {
            res(response.val());
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  /**
   * @param {string} offerId
   */
  getOffer(offerId) {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref(`auction_offers/${offerId}`)
        .on(
          "value",
          (response) => {
            res(response.val());
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  /**
   * @param {File} thumbnail
   */
  uploadThumbnail(thumbnail) {
    return new Promise((res, rej) => {
      const thumbnailRef = firebase.storage().ref(`auction/images/thumbnails/${thumbnail.name}`);
      const task = thumbnailRef.put(thumbnail);
      task.on(
        "state_changed",
        function progress(snapshot) {
          // Do something...
        },
        function error(error) {
          rej(error);
        },
        function complete(event) {
          thumbnailRef.getDownloadURL().then((url) => {
            res(url);
          });
        }
      );
    });
  }

  /**
   * @param {FileList} productImages
   */
  uploadImages(productImages) {
    return new Promise((res, rej) => {
      var images = [];
      for (let i = 0; i < productImages.length; i++) {
        const image = productImages[i];
        const imageRef = firebase.storage().ref(`auction/images/product/${image.name}`);
        const task = imageRef.put(image);
        task.on(
          "state_changed",
          function progress(snapshot) {
            // Do something...
          },
          function error(error) {
            rej(error);
          },
          function complete(event) {
            imageRef.getDownloadURL().then((url) => {
              images.push(url);
            });
          }
        );
      }
      res(images);
    });
  }

  /**
   * @param {object} offerData
   */
  createOffer(offerData) {
    return new Promise((res, rej) => {
      if (offerData.expiresAt > new Date().getTime() / 1000) {
        const date = new Date(),
          owner = offerData.owner;
        const offerId = `${owner.userId}${date.getDate()}${date.getMonth()}${date.getSeconds()}`;

        // Upload thumbnail
        const thumbnail = this.uploadThumbnail(offerData.thumbnail);

        // Upload product-images
        const images = offerData.images != null ? this.uploadImages(offerData.images) : "null";

        // Resolve upload-promises
        Promise.all([thumbnail, images]).then((value) => {
          const thumbnailURL = value[0];
          const productImages = value[1]; // Should be an array
          // Update database
          new Promise((res, rej) => {
            firebase
              .database()
              .ref(`auction_offers/${offerId}`)
              .set({
                offer: {
                  type: offerData.type,
                  title: offerData.title,
                  description: offerData.description,
                  price: offerData.price,
                  bids: "null",
                  buy: {
                    bought: false,
                    buyerId: "null",
                    buyerSteam64Id: "null",
                  },
                  images: {
                    thumbnail: thumbnailURL,
                    product: productImages,
                  },
                  expiresAt: offerData.expiresAt,
                },
                owner: {
                  userId: owner.userId,
                  username: owner.username,
                  steam64Id: owner.steamId,
                },
              })
              .catch((error) => {
                rej(error);
              });
          });
        });

        res(offerId);
      } else {
        rej("Angebot bereits abgelaufen");
      }
    });
  }

  /**
   * Update offer & register buying process
   * @param {string} offerId
   * @param {string} sellerId
   * @param {string} buyerId
   * @param {string} buyerSteam64Id
   */
  buy(offerId, sellerId, buyerId, buyerSteam64Id) {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref(`auction_offers/${offerId}/offer/buy`)
        .set({
          bought: true,
          buyerId: buyerId,
          buyerSteam64Id: buyerSteam64Id,
        })
        .then(() => {
          // Update buyers history-
          firebase
            .database()
            .ref(`auction_user/${buyerId}/history/bought`)
            .push({
              offerId: offerId,
              boughtAt: new Date().getTime() / 1000,
            });

          // Update sellers history
          firebase
            .database()
            .ref(`auction_user/${sellerId}/history/sold`)
            .push({
              offerId: offerId,
              buyerId: buyerId,
              buyerSteam64Id: buyerSteam64Id,
              soldAt: new Date().getTime() / 1000,
            });
        });
      res(true);
    });
  }

  /**
   * Add a new bid to an offer
   * @param {string} offerId
   * @param {string} userId
   * @param {string} userSteam64Id
   * @param {number} amount
   */
  bid(offerId, userId, userSteam64Id, amount) {
    return new Promise((res, rej) => {
      const bidData = {
        userId: userId,
        steam64Id: userSteam64Id,
        amount: amount,
        bidAt: new Date().getTime() / 1000,
      };
      firebase
        .database()
        .ref(`auction_offers/${offerId}/offer/bids`)
        .push(bidData)
        .then((value) => {
          res(value);
        })
        .catch((error) => {
          rej(error);
        });
    });
  }

  /**
   * @param {string} offerid
   */
  removeOffer(offerId) {
    firebase.database().ref(`auction_offers/${offerId}`).remove();
    // TODO Check if offer was successfull deleted
    return offerId;
  }

  /**
   * @param {string} userId
   */
  checkUserId(userId) {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref(`auction_user/${userId}`)
        .once(
          "value",
          (snapshot) => {
            if (snapshot.exists()) {
              res(true);
            } else {
              res(false);
            }
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  /**
   * @param {string} username
   * @param {string} password
   * @param {string} email
   */
  addUser(username, password, email) {
    if (username != "" && password != "") {
      var userId = `${username.substring(0, 3).toLowerCase()}${
        new Date().getMonth() + 1
      }${new Date().getDate()}`;
      const user = {
        username: username,
        email: email == "" ? null : email,
        password: new Password(password).hash(),
        history: { bought: "null", sold: "null" },
      };

      this.checkUserId(userId)
        .then((value) => {
          if (value == true) {
            //console.log(`UserId: ${userId} is taken`);
            userId += new Date().getSeconds(); // This should be an unique userId
            firebase.database().ref(`auction_user/${userId}`).set(user);
          } else {
            //console.log(`UserId: ${userId} is free`);
            firebase.database().ref(`auction_user/${userId}`).set(user);
          }
        })
        .catch((error) => {
          alert(error);
        });
      return userId;
    } else {
      return "Benutzername oder Passwort fehlerhaft";
    }
  }

  /**
   * @param {string} userId
   * @param {string} password
   * @param {string} email
   */
  updateUser(userId, password, email) {
    return new Promise((res, rej) => {
      var temp = {
        password: null,
        email: null,
      };
      firebase
        .database()
        .ref(`auction_user/${userId}`)
        .on(
          "value",
          (snapshot) => {
            const userData = snapshot.val();
            temp.password = userData.password;
            temp.email = userData.email;
          },
          (error) => {
            rej(error);
          }
        );

      if (email != temp.email) {
        firebase.database().ref(`auction_user/${userId}/email`).set(email);
      }
      if (password != "" && password != null) {
        if (new Password(password).hash() != temp.password) {
          firebase
            .database()
            .ref(`auction_user/${userId}/password`)
            .set(new Password(password).hash());
        }
      }
      res(true);
    });
  }

  /**
   * @param {string} userId
   */
  removeUser(userId) {
    firebase.database().ref(`auction_user/${userId}`).remove();
    // TODO Check if user was successfull deleted
    return userId;
  }

  getUsers() {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref("auction_user")
        .on(
          "value",
          (response) => {
            res(response.val());
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  /**
   * @param {string} userId
   */
  getUser(userId) {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref(`auction_user/${userId}`)
        .on(
          "value",
          (response) => {
            res(response.val());
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  isLoggedIn() {
    const res = new Cookie().get("dag_auction");
    return res != null && res != "" ? true : false;
  }

  /**
   * @param {string} username
   * @param {string} password
   */
  doLogin(username, password) {
    return new Promise((res, rej) => {
      firebase
        .database()
        .ref("auction_user")
        .on(
          "value",
          (response) => {
            let success = false;
            const userList = response.val();
            for (const key in userList) {
              if (
                userList[key].username == username &&
                userList[key].password == new Password(password).hash()
              ) {
                success = true;
                new Cookie().set("dag_auction", `{"username": "${username}", "userId": "${key}"}`);
              }
            }
            res(success);
          },
          (error) => {
            rej(error);
          }
        );
    });
  }

  /**
   * @param {string} username
   * @param {string} password
   * @param {string} email
   */
  doRegistration(username, password, email) {
    // TODO Check if username & password aren't empty
    return new Promise((res, rej) => {
      var userId = `${username.substring(0, 3).toLowerCase()}${
        new Date().getMonth() + 1
      }${new Date().getDate()}`;
      const user = {
        username: username,
        email: email == "" ? null : email,
        password: new Password(password).hash(),
        history: { bought: "null", sold: "null" },
      };
      // There isn't a big differnce between pushing or setting an user(with custom key)
      //firebase.database().ref("auction_user").push(user);
      this.checkUserId(userId)
        .then((value) => {
          if (value == true) {
            // userId is taken
            userId += new Date().getSeconds();
            firebase.database().ref(`auction_user/${userId}`).set(user);
            this.doLogin(username, password);
          } else {
            // userId is free
            firebase.database().ref(`auction_user/${userId}`).set(user);
            this.doLogin(username, password);
          }
        })
        .catch((error) => {
          alert(error);
        });
      res({ user: username, userId: userId });
    });
  }

  doLogout() {
    new Cookie().delete("dag_auction");
  }

  /**
   * @param {string} username
   */
  updateDropdown(username) {
    const data = {
      avatar: "https://files.dulliag.de/web/images/logo.jpg",
      profile: "https://dulliag.de/Auktionen/Profil/",
      offers: "#",
      messages: "#",
    };
    $("#auctionBar #signInBtn").remove();
    $("#auctionBar .row").append(`<div class="dropdown ml-auto mr-4">
      <button class="btn border-0" data-toggle="dropdown">
        <img class="rounded-circle" src="${data.avatar}" alt="Profilbild" style="width: 2.2rem; border: 2px solid #28A745;">
        </button>
      <div class="dropdown-menu dropdown-menu-right" style="z-index: 1200!important;">
        <a class="dropdown-item text-success text-center">${username}</a>
        <a class="dropdown-item" href="${data.profile}"><i class="far fa-user-circle"></i> Mein Profil</a>
        <a class="dropdown-item d-none" href="${data.offers}">Meine Angebote</a>
        <a class="dropdown-item d-none" href="${data.messages}">Meine Nachrichten</a>
        <a class="dropdown-item" href="#" onclick="new Cookie().delete('dag_auction'); location.reload();"><i class="fas fa-sign-out-alt"></i> Abmelden</a>
      </div>
    </div>`);
  }
}

class Password {
  /**
   * @param {string} password
   */
  constructor(password) {
    this.password = password;
  }

  /**
   * Check if an password is qual to an hashed password
   * @param {string} password
   */
  isEqualTo(password) {
    return password == this.hash(this.password) ? true : false;
  }

  /**
   * Generate an md5 hashed string
   * @param {string} string Password
   */
  hash() {
    var string = this.password;
    /**
     * Function by https://css-tricks.com/snippets/javascript/javascript-md5/#:~:text=Hashing%20the%20password%20with%20JavaScript%E2%80%99s%20only%20effect%20is,website%20by%20simply%20submitting%20the%20intercepted%20hashed%20password%2C
     */
    function RotateLeft(lValue, iShiftBits) {
      return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
    }

    function AddUnsigned(lX, lY) {
      var lX4, lY4, lX8, lY8, lResult;
      lX8 = lX & 0x80000000;
      lY8 = lY & 0x80000000;
      lX4 = lX & 0x40000000;
      lY4 = lY & 0x40000000;
      lResult = (lX & 0x3fffffff) + (lY & 0x3fffffff);
      if (lX4 & lY4) {
        return lResult ^ 0x80000000 ^ lX8 ^ lY8;
      }
      if (lX4 | lY4) {
        if (lResult & 0x40000000) {
          return lResult ^ 0xc0000000 ^ lX8 ^ lY8;
        } else {
          return lResult ^ 0x40000000 ^ lX8 ^ lY8;
        }
      } else {
        return lResult ^ lX8 ^ lY8;
      }
    }

    function F(x, y, z) {
      return (x & y) | (~x & z);
    }
    function G(x, y, z) {
      return (x & z) | (y & ~z);
    }
    function H(x, y, z) {
      return x ^ y ^ z;
    }
    function I(x, y, z) {
      return y ^ (x | ~z);
    }

    function FF(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function GG(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function HH(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function II(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function ConvertToWordArray(string) {
      var lWordCount;
      var lMessageLength = string.length;
      var lNumberOfWords_temp1 = lMessageLength + 8;
      var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
      var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
      var lWordArray = Array(lNumberOfWords - 1);
      var lBytePosition = 0;
      var lByteCount = 0;
      while (lByteCount < lMessageLength) {
        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
        lBytePosition = (lByteCount % 4) * 8;
        lWordArray[lWordCount] =
          lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition);
        lByteCount++;
      }
      lWordCount = (lByteCount - (lByteCount % 4)) / 4;
      lBytePosition = (lByteCount % 4) * 8;
      lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
      lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
      lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
      return lWordArray;
    }

    function WordToHex(lValue) {
      var WordToHexValue = "",
        WordToHexValue_temp = "",
        lByte,
        lCount;
      for (lCount = 0; lCount <= 3; lCount++) {
        lByte = (lValue >>> (lCount * 8)) & 255;
        WordToHexValue_temp = "0" + lByte.toString(16);
        WordToHexValue =
          WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
      }
      return WordToHexValue;
    }

    function Utf8Encode(string) {
      string = string.replace(/\r\n/g, "\n");
      var utftext = "";

      for (var n = 0; n < string.length; n++) {
        var c = string.charCodeAt(n);

        if (c < 128) {
          utftext += String.fromCharCode(c);
        } else if (c > 127 && c < 2048) {
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        } else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }
      }

      return utftext;
    }

    var x = Array();
    var k, AA, BB, CC, DD, a, b, c, d;
    var S11 = 7,
      S12 = 12,
      S13 = 17,
      S14 = 22;
    var S21 = 5,
      S22 = 9,
      S23 = 14,
      S24 = 20;
    var S31 = 4,
      S32 = 11,
      S33 = 16,
      S34 = 23;
    var S41 = 6,
      S42 = 10,
      S43 = 15,
      S44 = 21;

    string = Utf8Encode(string);

    x = ConvertToWordArray(string);

    a = 0x67452301;
    b = 0xefcdab89;
    c = 0x98badcfe;
    d = 0x10325476;

    for (k = 0; k < x.length; k += 16) {
      AA = a;
      BB = b;
      CC = c;
      DD = d;
      a = FF(a, b, c, d, x[k + 0], S11, 0xd76aa478);
      d = FF(d, a, b, c, x[k + 1], S12, 0xe8c7b756);
      c = FF(c, d, a, b, x[k + 2], S13, 0x242070db);
      b = FF(b, c, d, a, x[k + 3], S14, 0xc1bdceee);
      a = FF(a, b, c, d, x[k + 4], S11, 0xf57c0faf);
      d = FF(d, a, b, c, x[k + 5], S12, 0x4787c62a);
      c = FF(c, d, a, b, x[k + 6], S13, 0xa8304613);
      b = FF(b, c, d, a, x[k + 7], S14, 0xfd469501);
      a = FF(a, b, c, d, x[k + 8], S11, 0x698098d8);
      d = FF(d, a, b, c, x[k + 9], S12, 0x8b44f7af);
      c = FF(c, d, a, b, x[k + 10], S13, 0xffff5bb1);
      b = FF(b, c, d, a, x[k + 11], S14, 0x895cd7be);
      a = FF(a, b, c, d, x[k + 12], S11, 0x6b901122);
      d = FF(d, a, b, c, x[k + 13], S12, 0xfd987193);
      c = FF(c, d, a, b, x[k + 14], S13, 0xa679438e);
      b = FF(b, c, d, a, x[k + 15], S14, 0x49b40821);
      a = GG(a, b, c, d, x[k + 1], S21, 0xf61e2562);
      d = GG(d, a, b, c, x[k + 6], S22, 0xc040b340);
      c = GG(c, d, a, b, x[k + 11], S23, 0x265e5a51);
      b = GG(b, c, d, a, x[k + 0], S24, 0xe9b6c7aa);
      a = GG(a, b, c, d, x[k + 5], S21, 0xd62f105d);
      d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
      c = GG(c, d, a, b, x[k + 15], S23, 0xd8a1e681);
      b = GG(b, c, d, a, x[k + 4], S24, 0xe7d3fbc8);
      a = GG(a, b, c, d, x[k + 9], S21, 0x21e1cde6);
      d = GG(d, a, b, c, x[k + 14], S22, 0xc33707d6);
      c = GG(c, d, a, b, x[k + 3], S23, 0xf4d50d87);
      b = GG(b, c, d, a, x[k + 8], S24, 0x455a14ed);
      a = GG(a, b, c, d, x[k + 13], S21, 0xa9e3e905);
      d = GG(d, a, b, c, x[k + 2], S22, 0xfcefa3f8);
      c = GG(c, d, a, b, x[k + 7], S23, 0x676f02d9);
      b = GG(b, c, d, a, x[k + 12], S24, 0x8d2a4c8a);
      a = HH(a, b, c, d, x[k + 5], S31, 0xfffa3942);
      d = HH(d, a, b, c, x[k + 8], S32, 0x8771f681);
      c = HH(c, d, a, b, x[k + 11], S33, 0x6d9d6122);
      b = HH(b, c, d, a, x[k + 14], S34, 0xfde5380c);
      a = HH(a, b, c, d, x[k + 1], S31, 0xa4beea44);
      d = HH(d, a, b, c, x[k + 4], S32, 0x4bdecfa9);
      c = HH(c, d, a, b, x[k + 7], S33, 0xf6bb4b60);
      b = HH(b, c, d, a, x[k + 10], S34, 0xbebfbc70);
      a = HH(a, b, c, d, x[k + 13], S31, 0x289b7ec6);
      d = HH(d, a, b, c, x[k + 0], S32, 0xeaa127fa);
      c = HH(c, d, a, b, x[k + 3], S33, 0xd4ef3085);
      b = HH(b, c, d, a, x[k + 6], S34, 0x4881d05);
      a = HH(a, b, c, d, x[k + 9], S31, 0xd9d4d039);
      d = HH(d, a, b, c, x[k + 12], S32, 0xe6db99e5);
      c = HH(c, d, a, b, x[k + 15], S33, 0x1fa27cf8);
      b = HH(b, c, d, a, x[k + 2], S34, 0xc4ac5665);
      a = II(a, b, c, d, x[k + 0], S41, 0xf4292244);
      d = II(d, a, b, c, x[k + 7], S42, 0x432aff97);
      c = II(c, d, a, b, x[k + 14], S43, 0xab9423a7);
      b = II(b, c, d, a, x[k + 5], S44, 0xfc93a039);
      a = II(a, b, c, d, x[k + 12], S41, 0x655b59c3);
      d = II(d, a, b, c, x[k + 3], S42, 0x8f0ccc92);
      c = II(c, d, a, b, x[k + 10], S43, 0xffeff47d);
      b = II(b, c, d, a, x[k + 1], S44, 0x85845dd1);
      a = II(a, b, c, d, x[k + 8], S41, 0x6fa87e4f);
      d = II(d, a, b, c, x[k + 15], S42, 0xfe2ce6e0);
      c = II(c, d, a, b, x[k + 6], S43, 0xa3014314);
      b = II(b, c, d, a, x[k + 13], S44, 0x4e0811a1);
      a = II(a, b, c, d, x[k + 4], S41, 0xf7537e82);
      d = II(d, a, b, c, x[k + 11], S42, 0xbd3af235);
      c = II(c, d, a, b, x[k + 2], S43, 0x2ad7d2bb);
      b = II(b, c, d, a, x[k + 9], S44, 0xeb86d391);
      a = AddUnsigned(a, AA);
      b = AddUnsigned(b, BB);
      c = AddUnsigned(c, CC);
      d = AddUnsigned(d, DD);
    }

    var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

    return temp.toLowerCase();
  }
}
