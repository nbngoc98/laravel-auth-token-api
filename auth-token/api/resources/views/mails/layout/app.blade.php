<!doctype html>
<html lang="ja">
@include('mails.layout.template_header')
<body class="">
<table role="presentation" class="body">
    <tr>
        <td class="container">
            <div class="content">

                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table role="presentation">
                                <tr>
                                    <td>
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- END CENTERED WHITE CONTAINER -->

                <!-- START FOOTER -->
                @include('mails.layout.template_footer')
                <!-- END FOOTER -->

            </div>
        </td>
    </tr>
</table>
</body>
</html>
