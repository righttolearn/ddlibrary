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
                    <h3>Privacy Policy</h3>
                    <article>
                        <p>Right to Learn Afghanistan - Darakht-e Danesh understands and respects your need for privacy. Any
                            and all information collected at our sites will be kept strictly confidential and will not be
                            sold, reused, rented, loaned, or otherwise disclosed. Any information you give to Right to Learn
                            Afghanistan - Darakht-e Danesh will be held with the utmost care, and will not be used in ways
                            that you have not consented to. If you have any questions, please don&#39;t hesitate to contact
                            us.</p>

                        <h3>Automatic Information</h3>

                        <p>When you visit our Web site, our Web server automatically recognizes and stores the name of the
                            domain from which you access the Internet (for example, aol.com, if you are connecting from an
                            America Online account), the date and time you access our site, and the Internet Protocol (IP)
                            address of the Web site from where you came. This information enables us to run site usage tools
                            and create statistics about our site. These statistics help us to better understand how our site
                            is being used and what we can do to make it more useful to visitors. This information is not
                            stored in a personally-identifiable format.</p>

                        <h3>Information You Provide Us</h3>

                        <p>We use the information that you provide us for responding to your requests, customizing the
                            material presented to you based on your past visits to our web site, to communicate with you,
                            and to improve the content of our web site. The primary goal in collecting personal information
                            is to provide you customized services, features and content, and give you an efficient
                            personalized experience that best meets your needs.</p>

                        <p>If you choose to identify yourself by sending us an email or completing an online form, Right to
                            Learn Afghanistan - Darakht-e Danesh employees and business partners will have access to such
                            information in order to contact you for customer service purposes. We also aggregate information
                            on what pages you access. Information submitted by you, such as survey information and/or
                            comments or feedback, may be stored in a personally-identifiable format and is used only for
                            internal purposes and is not shared with people or organizations outside of Right to Learn
                            Afghanistan - Darakht-e Danesh, its subsidiaries or successors in interest, and its business
                            partners. If we want to share your name outside this group or otherwise publicize the fact that
                            you submitted materials or other information to us, then we will we ask your permission to use
                            your name with the materials or other information you submitted to a particular part of this
                            site.</p>

                        <h3>Links</h3>

                        <p>This site may contain links to other sites not owned or managed by Right to Learn Afghanistan -
                            Darakht-e Danesh. Right to Learn Afghanistan - Darakht-e Danesh is not responsible for the
                            privacy practices of such websites.</p>

                        <h3>Use of &quot;Cookies&quot;</h3>

                        <p>&quot;Cookies&quot; are small files that are placed on your hard drive that assist us in
                            providing customized services. We may use cookies to allow you to let enter your password less
                            frequently during a session. No other personal information is stored in this cookie.</p>

                        <p>From time to time, Right to Learn Afghanistan - Darakht-e Danesh may send a &quot;cookie&quot; to
                            your computer. A cookie is a small piece of data that is sent to your browser from a web server
                            and stored on your computer&rsquo;s hard drive. A cookie can&rsquo;t read data off your hard
                            disk or read cookie files created by other sites. Cookies do not damage your system. We use
                            cookies to identify which areas of the Right to Learn Afghanistan - Darakht-e Danesh websites
                            you have visited or customized, so the next time you visit; those pages may be readily
                            accessible. This information may be used to personalize our services to you.</p>

                        <p>You can choose whether to accept cookies by changing the settings of your browser. You can reset
                            your browser to refuse all cookies, or allow your browser to show you when a cookie is being
                            sent. If you choose not to accept these cookies, your experience at our site and other Web sites
                            may be diminished and some features may not work as intended.</p>

                        <h3>Opt Out</h3>

                        <p class="mb-0">Right to Learn Afghanistan - Darakht-e Danesh recognizes that users may not wish to
                            be contacted
                            about new or related products. Right to Learn Afghanistan - Darakht-e Danesh allows you to opt
                            out of any e-mail marketing that may result from use of the site. When you receive an email, you
                            will be given instructions on how to remove yourself from that list.</p>
                        <div class="mb-4">
                            <a href="{{ route('opt-out') }}">
                                @lang('Opt-out of Analytics')
                            </a>
                        </div>

                        <h3>Release of Information</h3>

                        <p>Right to Learn Afghanistan - Darakht-e Danesh will release any information that is required to be
                            released by law or court order.</p>

                        <p>Security of the Information we use secure socket layer (SSL) encryption to protect the
                            transmission of information you submit to us when you use our secure online forms. All the
                            information you provide us through these forms is stored securely offline. If you send us an
                            email or complete the Contact Us Form, you should know that email is not necessarily secure
                            against interception unless you are using a security-enabled web browser.</p>

                        <p>The above policies are effective as of Nov. 30, 2007. Right to Learn Afghanistan - Darakht-e
                            Danesh reserves the right to change this policy at any time. The use of information that we
                            gather now is subject to the Privacy Policy in effect at this time. Users wishing to see the
                            changes may do so by visiting our web site privacy policy statement frequently.</p>

                        <p><strong>For Darakht-e Danesh mobile application specific policy and terms of use, please <a
                                    href="{{ route('mobile-privacy-policy') }}">visit this link</a>.&nbsp;</strong>
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </div>
@endsection
