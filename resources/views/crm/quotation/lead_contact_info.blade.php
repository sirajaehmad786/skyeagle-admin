<div class="row">
    <div class="col-md-12"> 
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-start">
                            <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info ">Contact
                                    Details</span></h4>
                            <p class=" mb-2"><strong>Name :</strong> <span class="ms-2">{{ $lead->contact->name }}</span>
                            </p>
                            <p class=" mb-2"><strong>Mobile :</strong><span
                                    class="ms-2">{{ $lead->contact->mobile_no }}</span></p>

                            <p class=" mb-2"><strong>Email :</strong> <span
                                    class="ms-2 ">{{ $lead->contact->email }}</span></p>

                            <p class=" mb-1"><strong>Location :</strong> <span class="ms-2">
                                    @php
                                        $locationParts = array_filter([
                                            $lead->contact->city,
                                            $lead->contact->state,
                                            $lead->contact->country
                                        ]);
                                    @endphp
                                    {{ implode(', ', $locationParts) }}
                                </span></p>
                        </div>
                    </div>
                    @if(empty($onlyContact))
                    <div class="col-md-6">
                        <div class="text-start">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2">
                                <h4 class="card-title mb-0 text-info"><span class="border-bottom border-info">Lead Details</span></h4>
                                @can('lead-edit')
                                    <a href="{{ route('leads.edit', $lead->id) }}" target="_blank" class="btn btn-soft-info btn-sm flex-shrink-0">
                                        <i class="ri-edit-box-line me-1"></i> Edit Lead
                                    </a>
                                @endcan
                            </div>
                                <p class="mb-2"><strong>Query Code :</strong> <span
                                    class="ms-2 ">{{ $lead->lead_code }}</span>
                                </p>
                            <p class=" mb-2"><strong>Destination :</strong> 
                            
                            @if($lead->travel_type == 'Domestic')
                                @foreach(json_decode($lead->destination,true) as $key=>$destination)
                                    @if($key > 1) 
                                        {{ "|" }}
                                    @endif
                                    <span class="ms-2 me-2">{{ $destination['state'] }},{{{ $destination['city'] }}}</span> 
                                @endforeach
                            @else
                                @if(!empty($lead->destination))
                                    @foreach(json_decode($lead->destination,true) as $key=>$destination)
                                        @if($key > 1) 
                                            {{ "|" }}
                                        @endif
                                        <span class="me-2 ms-2">{{ isset($destination['country'])?$destination['country']:'' }}, {{ isset($destination['city'])?$destination['city']:'' }}</span> 
                                    @endforeach
                                @endif
                            @endif
                            </p>
                            <p class=" mb-2"><strong>Travel Destination Type :</strong><span class="ms-2">
                                   {{ $lead->travel_type }}
                                </span>
                            </p>
                            <p class=" mb-2"><strong>Travel Date :</strong><span class="ms-2">
                                    @if (!empty($lead->start_date) && !empty($lead->end_date))
                                        {{ formateDate($lead->start_date) }}
                                        <b>To</b> {{ formateDate($lead->end_date) }}
                                    @endif
                                </span></p>

                            <p class=" mb-2"><strong>Duration :</strong>
                                <span class="ms-2">{{ countDaysAndNights($lead->start_date, $lead->end_date, 1) }}</span>
                            </p>
                            <p class="mb-2"><strong>Hotel Category :</strong> <span
                                class="ms-2 ">{{ $lead->hotel_category }}</span></p>
                            <p class="mb-2"><strong>Package Type :</strong> <span
                                class="ms-2 ">{{ $lead->customer_category }}</span></p>
                            <p class="mb-2"><strong>Number of adults :</strong> <span
                                class="ms-2 ">{{ $lead->no_of_adults }}</span></p>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>