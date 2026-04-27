<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign in - AgriCRM</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/tabler/dist/css/tabler.css" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <link href="/tabler/dist/css/tabler-flags.css" rel="stylesheet" />
    <link href="/tabler/dist/css/tabler-socials.css" rel="stylesheet" />
    <link href="/tabler/dist/css/tabler-payments.css" rel="stylesheet" />
    <link href="/tabler/dist/css/tabler-vendors.css" rel="stylesheet" />
    <link href="/tabler/dist/css/tabler-marketing.css" rel="stylesheet" />
    <link href="/tabler/dist/css/tabler-themes.css" rel="stylesheet" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    <link href="/preview/css/demo.css" rel="stylesheet" />
    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
      @import url("https://rsms.me/inter/inter.css");
      :root {
      	--tblr-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
    <!-- END CUSTOM FONT -->
  </head>
  <body>
    <!-- BEGIN GLOBAL THEME SCRIPT -->
    <script src="/tabler/dist/js/tabler-theme.min.js"></script>
    <!-- END GLOBAL THEME SCRIPT -->
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-leaf text-primary" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
               <path d="M5 21c.5 -4.5 2.5 -8 7 -10"></path>
               <path d="M9 18c6.218 0 10.5 -3.288 11 -12v-2h-4.014c-9 0 -11.986 4 -12 9c0 1 0 3 2 5h3z"></path>
            </svg>
            <h1 class="m-0 mt-2">AgriCRM</h1>
          </a>
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Login to your account</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                        </div>
                        <div>
                            <ul class="mb-0 px-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
              @csrf
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" autocomplete="off" value="{{ old('email', $rememberEmail) }}" required />
              </div>
              <div class="mb-2">
                <label class="form-label">
                  Password
                  <span class="form-label-description">
                    <a href="#">I forgot password</a>
                  </span>
                </label>
                <div class="input-group input-group-flat">
                  <input type="password" name="password" class="form-control" placeholder="Your password" autocomplete="off" required />
                  <span class="input-group-text">
                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                        focusable="false"
                        class="icon icon-1"
                      >
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                      </svg>
                    </a>
                  </span>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-check">
                  <input type="checkbox" name="remember" class="form-check-input" @checked(old('remember') || $rememberEmail) />
                  <span class="form-check-label">Remember me on this device</span>
                </label>
              </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/tabler/dist/js/tabler.min.js" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
  </body>
</html>
