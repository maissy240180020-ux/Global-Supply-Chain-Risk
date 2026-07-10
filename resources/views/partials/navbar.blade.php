<nav class="navbar bg-white shadow-sm px-4">

    <h4 class="mb-0 fw-bold">

        @yield('title','Dashboard')

    </h4>

    <div class="d-flex align-items-center">

        <i class="bi bi-bell fs-4 me-4"></i>

        <div class="dropdown">

            <a
                class="d-flex align-items-center text-decoration-none dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                    class="rounded-circle me-2"
                    width="40">

                <strong class="text-dark">

                    {{ Auth::user()->name }}

                </strong>

            </a>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>

                    <span class="dropdown-item-text">

                        {{ Auth::user()->email }}

                    </span>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <form
                        action="{{ route('logout') }}"
                        method="POST">

                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item text-danger">

                            <i class="bi bi-box-arrow-right"></i>

                            Logout

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</nav>