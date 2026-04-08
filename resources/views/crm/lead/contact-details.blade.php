<div class="row">
    <div class="col-md-12"> 
        <div class="card">
            <div class="card-body">

                <h4 class="card-title pb-2 text-info">
                    <span class="border-bottom border-info">Contact Details</span>
                </h4>

                <p><strong>Name :</strong> {{ $lead->contact->name }}</p>
                <p><strong>Mobile :</strong> {{ $lead->contact->mobile_no }}</p>
                <p><strong>Email :</strong> {{ $lead->contact->email }}</p>

                <p>
                    <strong>Location :</strong>
                    @php
                        $locationParts = array_filter([
                            $lead->contact->city,
                            $lead->contact->state,
                            $lead->contact->country
                        ]);
                    @endphp
                    {{ implode(', ', $locationParts) }}
                </p>

            </div>
        </div>
    </div>
</div>
