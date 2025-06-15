<div class="w-full bg-white">
    <div>
        <div class="px-4">
            <center>
                <div class="box-logo z-10 w-24 lg:w-32 pt-4">
                    <img src="{{ @$company->logo ? asset(env('APP_URL') . '/' . $company->logo->url) : '' }}"
                        alt="logo" class="border-solid border-white border-2 rounded-full z-10 fade-in">
                </div>
                <div class="lg:mt-4">
                    <div class="flex flex-col w-full">
                        <div class="text-2xl lg:text-4xl font-bold leading-7 text-black">
                            {{ @$company->name }}
                        </div>
                        <div class="flex flex-col mb-2 mt-2"><span data-v-0c6d760c="" class="text-sm w-full lg:text-lg">
                                {{ @$company->slogan }}
                            </span>
                        </div>
                    </div>
                </div>
            </center>
        </div>
        <!---->
    </div>
</div>
