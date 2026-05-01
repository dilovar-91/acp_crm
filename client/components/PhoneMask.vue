<template>
  <div>
    <v-text-field
      ref="phoneMask"
      v-mask="mask"
      :value="innerValue"
      :label="placeholder"
      autofocus
      dense
      outlined
      type="text"
      :class="inputClass"
      @input="up"
    >
      <template #append-inner>
        <div style="width:30px">
          <CountryFlag v-if="country && showFlag" :country="country" :size="flagSize" :class="flagClass"/>
        </div>
      </template>
    </v-text-field>

  </div>
</template>

<script>
import {TheMask} from "vue-the-mask";
import {parsePhoneNumberFromString} from "libphonenumber-js";
import CountryFlag from "vue-country-flag";

import visitorInfo from "visitor-info";
import {
  getMaskToLibPhoneNumber,
  findFirstCountryByAlpha2,
  findFirstCountryByCode,
  isCanada
} from "./country_telephone_data.js";

export default {
  name: "PhoneMask",
  components: {TheMask, CountryFlag},
  props: {
    value: {
      type: [String, Number]
    },
    showFlag: {
      type: Boolean,
      default: false
    },
    autoDetectCountry: {
      type: Boolean,
      default: false
    },
    defaultCountry: {
      type: String
    },
    placeholder: {
      type: String
    },
    flagSize: {
      type: String,
      default: "normal"
    },
    disabled: {
      type: Boolean,
      default: false
    },
    wrapperClass: {
      type: String,
      default: "phone-mask-wrapper-lib"
    },
    inputClass: {
      type: String,
      default: "phone-input-lib"
    },
    flagClass: {
      type: String,
      default: "country-flag-lib"
    }
  },
  data() {
    return {
      innerValue: this.value,
      countryCode: "",
      mask: "+###########",
      defaultMask: "+###########",
      country: "RU",
      isValid: false,
      test: '',
      plusTokens: {
        "#": {
          pattern: /\d/
        },
        "*": {
          pattern: /\d|\+/
        }
      }
    };
  },
  computed: {
    currentNumber () {
      const plus = /^\+/.test(this.innerValue) || !this.innerValue ? "" : "+";
      const filteredNumberArr = this.innerValue
        ? this.innerValue.match(/[\d+]/g)
        : null;
      const filteredNumber = filteredNumberArr
        ? filteredNumberArr.join("")
        : "";

      return this.mask === this.defaultMask
        ? `${plus}${filteredNumber}`
        : filteredNumber;
    }
  },
  watch: {
    currentNumber(value) {
      this.$emit("input", value);
      this.$emit("onValidate", {number: value, isValidByLibPhoneNumberJs: this.isValid, country: this.country});
    }
  },
  beforeMount() {
    if (this.autoDetectCountry) {
      const visitorContryInfo = visitorInfo();
      this.visitorCountry =
        visitorContryInfo && visitorContryInfo.country
          ? visitorContryInfo.country.alpha2
          : "";
    }
    this.updateMaskData();
  },
  mounted() {
    this.$refs.phoneMask.$el.onblur = () => {
      this.$emit("onBlur")
    }
    this.$refs.phoneMask.$el.onfocus = () => {
      this.$emit("onFocus")
    }
  },
  methods: {
    onInput (value) {
      const filteredValue = value.match(/[\d+]/g);
      this.innerValue = filteredValue ? filteredValue.join("") : "";
      this.updateMaskData();

      this.$nextTick(function () {
        setTimeout(this.setFocusToEnd.bind(this), 0);
      });
    },

    up (e){
      // this.test = e
    },


    setFocusToEnd () {
        // const length = this.test.length;
        // this.test.focus();
        // this.$refs.phoneMask.$refs.input.setSelectionRange(length, length);
    },

    updateMaskData () {
      let {
        currentNumber,
        visitorCountry,
        innerValue,
        countryCode,
        defaultMask,
        autoDetectCountry,
        defaultCountry
      } = this;

      let phoneInfo = parsePhoneNumberFromString(currentNumber);

      if (!phoneInfo && !currentNumber) {
        const country = findFirstCountryByAlpha2(defaultCountry);
        if (country) {
          phoneInfo = {country: defaultCountry};
          autoDetectCountry = false;
          this.innerValue = `+${country.code}`;
        }
      }

      if (phoneInfo && !phoneInfo.country) {
        switch (phoneInfo.countryCallingCode) {
          case "+7": {
            phoneInfo.country = "RU";
            break;
          }
          case "8": {
            phoneInfo.country = "RU";
            break;
          }
          case "1": {
            if (currentNumber.length > 4)
              phoneInfo.country = isCanada(currentNumber) ? "CA" : "US";
            break;
          }
        }
      } else if (autoDetectCountry && visitorCountry && !innerValue)
        phoneInfo = {country: visitorCountry};
      else if (!phoneInfo && currentNumber.length > 2)
        phoneInfo = {country: findFirstCountryByCode(currentNumber)};

      const computedMask = getMaskToLibPhoneNumber(phoneInfo);
      const computedCountry =
        phoneInfo && phoneInfo.country ? phoneInfo.country.toLowerCase() : "";

      if (autoDetectCountry && visitorCountry) {
        if (!innerValue) this.innerValue = computedMask.countryCode;
        this.visitorCountry = null;
      }

      if (computedMask && computedMask.mask) {
        this.mask = computedMask.mask;
        this.country = computedCountry;
        this.countryCode = computedMask.countryCode;
        this.isValid = !!phoneInfo && !!phoneInfo.isValid && phoneInfo.isValid();
      } else if (countryCode.length > currentNumber.length) {
        this.mask = defaultMask;
        this.country = "";
        this.countryCode = "";
        this.isValid = false;
      }
    }
  }
};
</script>
