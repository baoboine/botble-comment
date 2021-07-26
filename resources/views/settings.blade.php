@extends('core/base::layouts.master')
@section('content')
    {!! Form::open(['route' => ['comment.storage-settings']]) !!}
    <div class="max-width-1200">
        <div class="flexbox-annotated-section">

            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>Comment settings</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">Configure comment options</p>
                </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">

                    <div class="form-group">
                        <input type="hidden" name="comment_enable" value="0">
                        <div class="form-check p-0">
                            <input class="form-check-input" type="checkbox" name="comment_enable"
                                   @if (setting('comment_enable')) checked @endif
                                   value="1" id="comment-enable">
                            <label class="form-check-label" for="enable">
                                Enable?
                            </label>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div id="show-comments-setting" style="display: none">

            <div class="flexbox-annotated-section">

                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>Enable comments for</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">Select Modules to Embed Comments</p>
                    </div>
                </div>

                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        @php
                            $menuEnables = json_decode(setting('comment_menu_enable', '[]'), true);
                        @endphp
                        <div class="form-group">

                            <input type="hidden" name="comment_menu_enable[]" value="0" />

                            @foreach(SlugHelper::supportedModels() as $model => $name)
                                <div class="form-check p-0">
                                    <input class="form-check-input" type="checkbox"
                                           @if (in_array($model, $menuEnables)) checked @endif
                                           value="{{ $model }}" name="comment_menu_enable[]" id="{{ $model }}">
                                    <label class="form-check-label" for="{{ $model }}">
                                        {{ $name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>

            <div class="flexbox-annotated-section">

                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>Appearance</h2>
                    </div>
                </div>

                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <input type="hidden" name="plugin_comment_rating" value="0">
                            <input class="form-check-input" type="checkbox" name="plugin_comment_rating"
                                   @if (setting('plugin_comment_rating', true)) checked @endif
                                   value="1" id="plugin_comment_rating"
                            >

                            <label class="form-check-label" for="enable">
                                Enable Rating
                            </label>
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="plugin_comment_reactions" value="0">
                            <input class="form-check-input" type="checkbox" name="plugin_comment_reactions"
                                   @if (setting('plugin_comment_reactions')) checked @endif
                                   disabled
                                   value="1" id="plugin_comment_reactions"
                            >
                            <label class="form-check-label" for="enable">
                                Enable Reactions
                            </label>

                            {{ Form::helper('Coming soon') }}
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="plugin_comment_profiles" value="0">
                            <input class="form-check-input" type="checkbox" name="plugin_comment_profiles"
                                   @if (setting('plugin_comment_profiles')) checked @endif
                                    disabled
                                   value="1" id="plugin_comment_profiles"
                            >
                            <label class="form-check-label" for="enable">
                                Enable member profile
                            </label>

                            {{ Form::helper('Coming soon') }}
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Copyright</label>
                            <input type="text" class="form-control" name="plugin_comment_copyright" value="{{ setting('plugin_comment_copyright', 'Proudly Powered by Bao Boi') }}" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="flexbox-annotated-section">

                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>Update Version</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">Make sure your Plugin is always up to date</p>
                    </div>
                </div>

                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div id="comment-plugin-updater"
                             data-msg="{{ $update_step_msg }}"
                             data-apis='@json([
                                'check' => route('comment.updater-check'),
                                'download' => route('comment.updater-download')
                            ])'
                        >

                            <div class="form-group">
                                <label class="text-sm not-italic font-weight-bold input-label d-block">Current Version</label>
                                <p>You can easily update Plugin by checking for a new update by clicking the button below</p>
                                <label class="box-border flex w-16 p-3 my-2 text-sm text-gray-500 bg-gray-200 border border-gray-200 border-solid rounded-md" id="version">
                                    {{ comment_plugin_version() }}
                                </label>

                                <hr />

                                @if (!empty($can_update))
                                    @foreach ($can_update as $message)
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @endforeach
                                @else
                                    @if(!$update_step)
                                        <button class="btn btn-outline-primary" type="button" id="check-version"><i class="fas fa-sync-alt"></i> Check for Updates</button>
                                    @else
                                        <div class="alert alert-info" style="padding:.75rem 12px">
                                            <i class="fas fa-arrow-alt-circle-right"></i> {{ $update_step_msg }}
                                        </div>
                                        <button class="btn btn-warning has-new-version" type="button" id="check-version"><i class="fas fa-arrow-alt-circle-right"></i> Continue update</button>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
