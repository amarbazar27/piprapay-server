const cutElement = (wrapper) => {
  const $wrapper = document.createElement('div')
  $wrapper.innerHTML = wrapper.children[0].outerHTML
  return $wrapper.children[0]
}

new Promise((resolve, reject) => {
  const settings = {
    skin: HSThemeAppearance.getAppearance() || 'default',
    layout: window.localStorage.getItem('layout') || 'default',
    fluid: window.localStorage.getItem('builderFluidSwitch') || false,
    sidebarNavOptions: window.localStorage.getItem('sidebarNavOptions') || 'pills',
  }

  const checkFluid = function () {
    let $contentContainers;
    if (settings.fluid === 'true') {
      document.querySelectorAll('header .container').forEach(function ($container) {
        $container.classList.remove('container')
        $container.classList.add('container-fluid')
      })
      $contentContainers = document.querySelectorAll('.content.container')
      $contentContainers.forEach(function ($contentContainer) {
        $contentContainer.classList.remove('container')
        $contentContainer.classList.add('container-fluid')
      })
    } else {
      $contentContainers = document.querySelectorAll('.content.container-fluid')
      $contentContainers.forEach(function ($contentContainer) {
        $contentContainer.classList.remove('container-fluid')
        $contentContainer.classList.add('container')
      })
    }
  }

  const initHeader = function () {
    const $script = document.createElement('script')
    $script.innerText = new HSMegaMenu('.js-mega-menu', {
      desktop: {
        position: 'left'
      }
    })

    window.addEventListener('load', function () {
      document.body.appendChild($script)
      setTimeout(function () {
        window.scrollTo(0, 0)
      })
    })
  }

  // Set vartical navar pills/tab style
  if (settings.sidebarNavOptions === 'tabs') {
    $navPills = document.querySelector('.nav-pills')
    if ($navPills) {
      $navPills.classList.remove('nav-pills')
      $navPills.classList.add('nav-tabs')
    }
  }

  // Set layout
  if (settings.layout === 'default') {
    // Default layout - no changes needed
  } else if (settings.layout === 'navbar-dark') {
    const $aside = document.querySelector('.navbar-vertical-aside')
    if ($aside) {
      $aside.classList.remove('bg-white')
      $aside.classList.add('bg-dark', 'navbar-dark')
    }
  }

  return resolve(true)
}).then(function () {
  // Show body after build
  document.body.style.opacity = 1

  // Clean up localStorage items that are no longer needed
  window.localStorage.removeItem('layout')
  window.localStorage.removeItem('builderFluidSwitch')
  window.localStorage.removeItem('sidebarNavOptions')
})