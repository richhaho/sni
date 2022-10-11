const DomUtils = {
  getEl: (selector) => window.document.querySelector(selector),

  hasClass: (el, cssClass) => {
    if (el.classList) {
      return el.classList.contains(cssClass);
    }
    return !!el.className.match(new RegExp(`(\\s|^)${cssClass}(\\s|$)`));
  },

  removeClass: (el, cssClass) => {
    if (el.classList) {
      el.classList.remove(cssClass);
    } else if (DomUtils.hasClass(el, cssClass)) {
      const reg = new RegExp(`(\\s|^)${cssClass}(\\s|$)`);
      el.className = el.className.replace(reg, ' ');
    }
  },
};

const SubmitButton = {
  buttonElement: DomUtils.getEl('[data-submit-btn]'),
  loaderElement: DomUtils.getEl('.btn__loader'),

  enable: () => {
    SubmitButton.buttonElement.disabled = false;
    DomUtils.removeClass(SubmitButton.buttonElement, 'disabled-bkg');
  },

  setSubmitState: () => {
    SubmitButton.buttonElement.disabled = true;
    SubmitButton.loaderElement.style.display = 'inline-block';
  },

  removeSubmitState: () => {
    SubmitButton.buttonElement.disabled = false;
    SubmitButton.loaderElement.style.display = 'none';
  }
};

const config = {
  fields: {
    card: {
      selector: '[data-cc-card]',
    },
    cvv: {
      selector: '[data-cc-cvv]',
    },
    exp: {
      selector: '[data-cc-exp]',
    },
    name: {
      selector: '[data-cc-name]',
      placeholder: 'Full Name',
    },
  },

  // css classes to be injected into the iframes.
  // the properties allowed are restricted via whitelist.
  // further, unrestricted styling can be applied to the div's in which the iframes are injected.
  styles: {
    input: {
      'font-size': '16px',
      color: '#00a9e0',
      'font-family': 'monospace',
      background: 'black',
    },
    '.card': {
      'font-family': 'monospace',
    },
    ':focus': {
      color: '#00a9e0',
    },
    '.valid': {
      color: '#43B02A',
    },
    '.invalid': {
      color: '#C01324',
    },
    '@media screen and (max-width: 700px)': {
      input: {
        'font-size': '18px',
      },
    },
    'input:-webkit-autofill': {
      '-webkit-box-shadow': '0 0 0 50px white inset',
    },
    'input:focus:-webkit-autofill': {
      '-webkit-text-fill-color': '#00a9e0',
    },
    'input.valid:-webkit-autofill': {
      '-webkit-text-fill-color': '#43B02A',
    },
    'input.invalid:-webkit-autofill': {
      '-webkit-text-fill-color': '#C01324',
    },
    'input::placeholder': {
      color: '#aaa',
    },
  },

  // these values correspond to css class names defined above
  classes: {
    empty: 'empty',
    focus: 'focus',
    invalid: 'invalid',
    valid: 'valid',
  },
};

function authorizeSession(callback) {
  let request = new XMLHttpRequest();
  request.onload = () => {
    if (request.status >= 200 && request.status < 300) {
      callback(JSON.parse(request.responseText));
    } else {
      throw new Error("error response: " + request.responseText);
    }
    request = null;
  };
  request.open("POST", "/api/authorize-session", true);
  request.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
  request.send();
}

const hooks = {
  preFlowHook: authorizeSession,
};

const onCreate = (paymentForm) => {
  const onSuccess = (clientToken) => {
    $('#payment-success > span').html("Tokenization request sent! Please wait while review result.");
    $('#payment-success').removeClass('hidden');
    
    setTimeout(
      function()
      {
        $('#payment-success').addClass('hidden');
        $.get('/api/verify-tokenize-response?client_token='+clientToken, function(data){
          if (data.status=='success'){
            $('#payment-success > span').html(data.description);
            $('#payment-success').removeClass('hidden');
          }else{
            $('#payment-errors > span').html(data.description);
            $('#payment-errors').removeClass('hidden');
          }
          
          setTimeout(
            function()
            {
              SubmitButton.removeSubmitState();
              paymentForm.reset(() => {});
              window.location.replace('/client/creditcard/');
            }, 3000);

        });
      }, 3000);
  };

  const onError = (error) => {
    $('#payment-errors > span').html("Tokenization request error: \"" + error.message + "\"");
    $('#payment-errors').removeClass('hidden');
    setTimeout(
      function()
      {
        SubmitButton.removeSubmitState();
        paymentForm.reset(() => {});
        $('#payment-errors').addClass('hidden');
      }, 3000);
  };
  const form = DomUtils.getEl('#form')
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    SubmitButton.setSubmitState();
    paymentForm.onSubmit(onSuccess, onError);
  });

  const ccFields = window.document.getElementsByClassName('payment-fields');
  for (let i = 0; i < ccFields.length; i++) {
    DomUtils.removeClass(ccFields[i], 'disabled');
  }
  SubmitButton.enable();
};

window.firstdata.createPaymentForm(config, hooks, onCreate);

