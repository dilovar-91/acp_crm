export default (context, inject) => {
  const range = (start, end, step = 1) => {
    const years = []
    if (start > end) {
      for (let i = start; i >= end; i = i - step) {
        if (step === 1) {
          years.push(i)
        } else {
          years.push(i.toFixed(1))
        }
      }
    } else {
      for (let i = start; i <= end; i = i + step) {
        if (step === 1) {
          years.push(i)
        } else {
          years.push(i.toFixed(1))
        }
      }
    }
    return years
  }

  const checkAccess = (field, slug) => {
    const permission = context.store.state.permission.user_permissions
    if (permission === null) {
      return false
    } else {
      const item = permission.find(p => p.page && p.page.slug === slug)
      return (item && item[field] !== 1)
    }
  }
  const truncate = (str, maxlength) => {
    return (str.length > maxlength) ?
      str.slice(0, maxlength - 1) : str;
  }

  const parseDate = (date) => {
    if (!date) {
      return null
    }
    return context.$moment(date).format('YY-MM-dd')
  }

  const notify = (type='success', message=null, timeout=5000) => {
    context.$toast[type](message, {
      position: 'top-right',
      timeout,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: true,
      closeButton: 'button',
      icon: true,
      rtl: false
    })
  }

  const colorLookup = {
    1: { color: "primary", textColor: "white" },
    2: { color: "success", textColor: "white" },
    4: { color: "error", textColor: "white" },
    5: { color: "warning", textColor: "white" },
    6: { color: "purple", textColor: "white" },
    7: { color: "blue", textColor: "white" },
    8: { color: "deep-orange", textColor: "white" },
    10: { color: "blue-grey", textColor: "white" },
    11: { color: "pink", textColor: "white" },
  }
  const getChipColor = (showroom) => {
    if (showroom?.id in colorLookup) {
      return colorLookup[showroom.id].color;
    } else {
      return "green";
    }
  }


  const getChipTextColor = (showroom) => {
    if (showroom?.id in colorLookup) {
      return colorLookup[showroom.id].textColor;
    } else {
      return "white";
    }
  }

  const formatUrl = (url) => {
    if (!url) return '';

    if (!/^https?:\/\//i.test(url)) {
      return 'https://' + url;
    }

    return url;
  };



  inject('hasDomain', (text) => {
    const domainPattern = /(\.ru|\.com|\.pro|\.net|\.su|\.рф|\.xn--[a-z0-9\-]+)(\b|\/|$)/i;
    const cleanedText = text?.trim();
    return domainPattern.test(cleanedText);
  });

  inject('range', range)
  inject('checkAccess', checkAccess)
  inject('parseDate', parseDate)
  inject('truncate', truncate)
  inject('getChipColor', getChipColor)
  inject('getChipTextColor', getChipTextColor)
  inject('notify', notify)
  inject('formatUrl', formatUrl);
}
