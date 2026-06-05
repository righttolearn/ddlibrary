@extends('layouts.main')

@section('title')
    @lang('Privacy Policy')
@endsection

@section('description')
    @lang('Privacy Policy - Darakht-e Danesh Library')
@endsection

@section('page_image')
    {{ getFile('public/img/logo-ddl-new.png') }}
@endsection

@push('styles')
    <style>
        .policies-content {
            line-height: 1.8;
        }
    </style>
@endpush

@section('content')
    <div class="container my-4">
        <div class="policies-content rounded shadow-sm">
            <!-- Analytics Opt-Out Section -->
            <div class="card border mt-4">
                <div class="card-body p-4">
                    <article>
                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:29.0pt"><span style="background-color:null">Privacy
                                                    Policy</span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null"> - Darakht-e Danesh understands and respects your
                                            need for privacy. Any and all information collected via our app will be kept
                                            strictly confidential and will not be sold, reused, rented, loaned, or otherwise
                                            disclosed. Any information you give to </span></span></span></span><span
                                style="font-size:12pt"><span style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan&nbsp;</span></span></span></span><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">- Darakht-e Danesh will be held with the utmost
                                            care, and will not be used in ways that you have not consented to. If you have
                                            any questions, please don&#39;t hesitate to contact
                                            us.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:18.5pt"><span style="background-color:null">Data
                                                    Collection</span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">We collect and store all personal information
                                            related to your user profile, which you voluntarily give use through sign-up or
                                            login: email, first and last name, country, city, and gender. If you choose to
                                            authenticate via Google you grant access to your Google profile. The app also
                                            automatically collects user id, browser name, type, and platform, as well as
                                            resource views in aggregate.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">The information collected is used to customize
                                            your browsing experience, enabling you to use the app features and maintaining
                                            your account. We may communicate with you about the app by sending you
                                            announcements, updates, and security alerts, and responding to your questions
                                            and feedback. Technical data collection is also used to support troubleshooting
                                            of technical issues and app improvement. Resource views are used for collection
                                            development and planning purposes. We may use aggregate details about app users
                                            to perform statistical analysis for internal data reporting
                                            purposes.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-family:Times New Roman,serif"><span style="font-size:16px"><span
                                            style="background-color:null">Your email and name will be shared with MailChimp
                                            in order to send you newsletters. Your data will also be shared with Google if
                                            you choose to authenticate via Google.&nbsp;</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:18.5pt"><span style="background-color:null">Data Storage
                                                    and Retention</span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="font-size:12pt"><span
                                    style="font-family:&quot;Times New Roman&quot;,serif"><span style="color:null"><span
                                            style="background-color:null">Data collected via the app directly is stored in
                                            Amazon RDS for no longer than one year. Data connected to your user profile is
                                            shared with the web version of Darakht-e Danesh and will be kept as long as you
                                            remain a registered user. In particular this profile will persist even if you
                                            choose to delete the app. If you want your profile to be deleted from our
                                            systems please contact us at </span></span><a
                                        href="mailto:admin@darakhtdanesh.org"
                                        style="color:#0563c1; text-decoration:underline"><span style="color:null"><span
                                                style="background-color:null">admin@darakhtdanesh.org</span></span></a></span></span>
                        </p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:18.5pt"><span
                                                    style="background-color:null">Links</span></span></strong></span></span></span>
                        </p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">This app may contain links to websites not owned
                                            or managed by </span></span></span></span><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null"> - Darakht-e Danesh.
                                        </span></span></span></span><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:12pt"><span style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null"> - Darakht-e Danesh is not responsible for the
                                            privacy practices of such websites.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:18.5pt"><span style="background-color:null">Release of
                                                    Information</span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null"> - Darakht-e Danesh will release any information
                                            that is required to be released by law or court
                                            order.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:18.5pt"><span style="background-color:null">Security of
                                                    the Information </span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">The app is built on a recent framework supported
                                            by Apple to ensure information security. We use secure socket layer (SSL)
                                            encryption to protect the transmission of information you submit to us when you
                                            use our secure online forms. All the information you provide us through these
                                            forms is stored securely offline. If you send us an email or complete the
                                            Contact Us Form, you should know that email is not necessarily secure against
                                            interception unless you are using a security-enabled web
                                            browser.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">Access to your user account is protected using an
                                            alphanumeric password. We recommend following best practices with regards to
                                            password generation.</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><strong><span
                                                style="font-size:29.0pt"><span style="background-color:null">Terms of
                                                    Use</span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><strong><span
                                                style="font-size:18.0pt"><span
                                                    style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                        style="background-color:null">Acceptance of
                                                        Terms</span></span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh makes available for
                                                    your use on this app information, documents, software and products
                                                    (collectively, the &quot;Materials&quot;) and various services (the
                                                    &quot;Services&quot;), subject to the terms and conditions set forth in
                                                    this document (the &quot;Terms of Use&quot;). By downloading, you agree
                                                    to the Terms of Use. </span></span></span></span></span></span><span
                                style="font-size:12pt"><span style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh reserves the right to
                                                    change the Terms of Use from time to time at its sole discretion. Your
                                                    use of the app will be subject to the most current version of the Terms
                                                    of Use at the time of such use. In addition, when using particular
                                                    Services or Materials on this app, you shall be subject to any posted
                                                    guidelines or rules applicable to such Services or Materials that may
                                                    contain terms and conditions in addition to those in the Terms of Use.
                                                    All such guidelines or rules are hereby incorporated by reference into
                                                    the Terms of Use. If you breach any of the Terms of Use, your
                                                    authorization to use this app automatically terminates and you must
                                                    immediately destroy any Materials downloaded or printed from the
                                                    app.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">Information on this app may contain
                                                    technical inaccuracies or typographical errors. Information may be
                                                    changed or updated without notice.
                                                </span></span></span></span></span></span><span
                                style="font-size:12pt"><span style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh may also make
                                                    improvements and/or changes in the products and/or the programs
                                                    described in this information at any time without
                                                    notice.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="font-size:12pt"><span
                                    style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh does not want to
                                                    receive confidential or proprietary information from you. Please note
                                                    that any information or material sent to
                                                </span></span></span></span></span></span><span
                                style="font-size:12pt"><span style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh will be deemed NOT to
                                                    be confidential. We will not release your name or otherwise publicize
                                                    the fact that you submitted materials or other information to us unless:
                                                    (a) we ask your permission to use your name; or (b) we first notify you
                                                    that the materials or other information you submit to a particular part
                                                    of this site will be published or otherwise used with your name on it;
                                                    or (c) we are required to do so by law. You can learn more about
                                                </span></span></span></span></span></span><span
                                style="font-size:12pt"><span style="background-color:white"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="color:#333333">Right to Learn
                                            Afghanistan</span></span></span></span><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null"> - Darakht-e Danesh privacy practices on
                                                    the web.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><strong><span
                                                style="font-size:18.0pt"><span
                                                    style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                        style="background-color:null">Copyright
                                                        Notice</span></span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">The content and design of the Darakht-e
                                                    Danesh app (excluding the Library contents themselves) is copyright
                                                    &copy; Darakht-e Danesh 2008 &amp; 2016. You agree that any copy of
                                                    documents you make shall retain all copyright and other proprietary
                                                    notices contained herein, and will attribute Darakht-e Danesh as the
                                                    source. You may not alter the content of this web site in any manner or
                                                    use it for commercial purposes. If you are interested in using the
                                                    contents of this website except as prescribed above, please contact us
                                                    directly.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">The materials included in the Library
                                                    have copyright information specific to each individual document. Please
                                                    observe the designations provided on the resource description pages.
                                                    Wherever possible resources are under a license that allows for free
                                                    reuse and adaptation. When reusing materials please attribute Darakht-e
                                                    Danesh Library and any identified author. If you have questions or
                                                    concerns about the copyright status of any resources available herein,
                                                    please contact us.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">All trademarks mentioned herein belong to
                                                    their respective owners. Nothing contained herein shall be construed as
                                                    conferring by implication, estoppels or otherwise any license or right
                                                    under any patent, trademark, copyright, or other right of Darakht-e
                                                    Danesh or any third party.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><strong><span
                                                style="font-size:18.0pt"><span
                                                    style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                        style="background-color:null">App Store User
                                                        License</span></span></span></strong></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">Apps made available through the App Store
                                                    are licensed, not sold, to you. Your license to the App is subject to
                                                    your prior acceptance of this Terms of Use. This license is a
                                                </span><strong><span style="background-color:null">non-exclusive,
                                                        non-transferable, non-sharable, revocable, limited license to use
                                                        the app/service solely for personal, non-commercial use in
                                                        accordance with the terms of use. </span></strong><span
                                                    style="background-color:null">Except as provided in the Usage Rules,
                                                    you may not distribute or make the Licensed Application available over a
                                                    network where it could be used by multiple devices at the same time. You
                                                    may not transfer, redistribute or sublicense the Licensed Application
                                                    and, if you sell your Apple Device to a third party, you must remove the
                                                    Licensed Application from the Apple Device before doing so. You may not
                                                    copy (except as permitted by this license and the Usage Rules),
                                                    reverse-engineer, disassemble, attempt to derive the source code of,
                                                    modify, or create derivative works of the Licensed Application, any
                                                    updates, or any part thereof (except as and only to the extent that any
                                                    foregoing restriction is prohibited by applicable law or to the extent
                                                    as may be permitted by the licensing terms governing use of any
                                                    open-sourced components included with the Licensed
                                                    Application).</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-size:12.0pt"><span
                                                style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                    style="background-color:null">You agree that Licensor may collect and
                                                    use technical data and related information&mdash;including but not
                                                    limited to technical information about your device, system and
                                                    application software, and peripherals&mdash;that is gathered
                                                    periodically to facilitate the provision of software updates, product
                                                    support, and other services to you (if any) related to the Licensed
                                                    Application. Licensor may use this information, as long as it is in a
                                                    form that does not personally identify you, to improve its products or
                                                    to provide services or technologies to you. This agreement is effective
                                                    until terminated by you or
                                                    Licensor.</span></span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:12pt"><span
                                        style="font-family:&quot;Times New Roman&quot;,serif"><span
                                            style="background-color:null">The Licensed Application may enable access to
                                            Licensor&rsquo;s and/or third-party services and websites (collectively and
                                            individually, &quot;External Services&quot;). You agree to use the External
                                            Services at your sole risk. Licensor is not responsible for examining or
                                            evaluating the content or accuracy of any third-party External Services, and
                                            shall not be liable for any such third-party External Services. Data displayed
                                            by any Licensed Application or External Service, including but not limited to
                                            financial, medical and location information, is for general informational
                                            purposes only and is not guaranteed by Licensor or its agents. You will not use
                                            the External Services in any manner that is inconsistent with the terms of use
                                            or that infringes the intellectual property rights of Licensor or any third
                                            party. You agree not to use the External Services to harass, abuse, stalk,
                                            threaten or defame any person or entity, and that Licensor is not responsible
                                            for any such use. External Services may not be available in all languages or in
                                            your Home Country, and may not be appropriate or available for use in any
                                            particular location. To the extent you choose to use such External Services, you
                                            are solely responsible for compliance with any applicable laws. Licensor
                                            reserves the right to change, suspend, remove, disable or impose access
                                            restrictions or limits on any External Services at any time without notice or
                                            liability to you.&nbsp;</span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm">&nbsp;</p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                style="background-color:null">YOU EXPRESSLY ACKNOWLEDGE AND AGREE THAT USE
                                                OF THE LICENSED APPLICATION IS AT YOUR SOLE RISK. TO THE MAXIMUM EXTENT
                                                PERMITTED BY APPLICABLE LAW, THE LICENSED APPLICATION AND ANY SERVICES
                                                PERFORMED OR PROVIDED BY THE LICENSED APPLICATION ARE PROVIDED &quot;AS
                                                IS&quot; AND &ldquo;AS AVAILABLE,&rdquo; WITH ALL FAULTS AND WITHOUT
                                                WARRANTY OF ANY KIND, AND LICENSOR HEREBY DISCLAIMS ALL WARRANTIES AND
                                                CONDITIONS WITH RESPECT TO THE LICENSED APPLICATION AND ANY SERVICES, EITHER
                                                EXPRESS, IMPLIED, OR STATUTORY, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
                                                WARRANTIES AND/OR CONDITIONS OF MERCHANTABILITY, OF SATISFACTORY QUALITY, OF
                                                FITNESS FOR A PARTICULAR PURPOSE, OF ACCURACY, OF QUIET ENJOYMENT, AND OF
                                                NONINFRINGEMENT OF THIRD-PARTY RIGHTS. NO ORAL OR WRITTEN INFORMATION OR
                                                ADVICE GIVEN BY LICENSOR OR ITS AUTHORIZED REPRESENTATIVE SHALL CREATE A
                                                WARRANTY. SHOULD THE LICENSED APPLICATION OR SERVICES PROVE DEFECTIVE, YOU
                                                ASSUME THE ENTIRE COST OF ALL NECESSARY SERVICING, REPAIR, OR CORRECTION.
                                                SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES OR
                                                LIMITATIONS ON APPLICABLE STATUTORY RIGHTS OF A CONSUMER, SO THE ABOVE
                                                EXCLUSION AND LIMITATIONS MAY NOT APPLY TO
                                                YOU.</span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                style="background-color:null">TO THE EXTENT NOT PROHIBITED BY LAW, IN NO
                                                EVENT SHALL LICENSOR BE LIABLE FOR PERSONAL INJURY OR ANY INCIDENTAL,
                                                SPECIAL, INDIRECT, OR CONSEQUENTIAL DAMAGES WHATSOEVER, INCLUDING, WITHOUT
                                                LIMITATION, DAMAGES FOR LOSS OF PROFITS, LOSS OF DATA, BUSINESS
                                                INTERRUPTION, OR ANY OTHER COMMERCIAL DAMAGES OR LOSSES, ARISING OUT OF OR
                                                RELATED TO YOUR USE OF OR INABILITY TO USE THE LICENSED APPLICATION, HOWEVER
                                                CAUSED, REGARDLESS OF THE THEORY OF LIABILITY (CONTRACT, TORT, OR OTHERWISE)
                                                AND EVEN IF LICENSOR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
                                                SOME JURISDICTIONS DO NOT ALLOW THE LIMITATION OF LIABILITY FOR PERSONAL
                                                INJURY, OR OF INCIDENTAL OR CONSEQUENTIAL DAMAGES, SO THIS LIMITATION MAY
                                                NOT APPLY TO YOU. In no event shall Licensor&rsquo;s total liability to you
                                                for all damages (other than as may be required by applicable law in cases
                                                involving personal injury) exceed the amount of fifty dollars ($50.00). The
                                                foregoing limitations will apply even if the above stated remedy fails of
                                                its essential purpose.</span></span></span></span></span></p>

                        <p style="margin-left:0cm; margin-right:0cm"><span style="color:null"><span
                                    style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span
                                            style="font-family:&quot;Times New Roman&quot;,serif"><span
                                                style="background-color:null">You may not use or otherwise export or
                                                re-export the Licensed Application except as authorized by Canadian law and
                                                the laws of the jurisdiction in which the Licensed Application was obtained.
                                            </span></span></span></span></span></p>
                    </article>

                    <div class="mb-4">
                        <a href="{{ route('opt-out') }}" class="btn btn-primary mt-2" onclick="gaOptout()">
                            @lang('Opt-out of Analytics')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
