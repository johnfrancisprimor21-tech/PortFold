{{-- Variables: $i (row index), $r (array of saved/old values) --}}
<div class="repeatable-item" data-row>
    <button type="button" class="remove-btn" aria-label="Remove this education entry" onclick="removeRow(this)">&times;</button>
    <div class="form-row">
        <div class="form-group">
            <label for="edu-inst-{{ $i }}">School / University</label>
            <input type="text" id="edu-inst-{{ $i }}" name="education[{{ $i }}][institution]" value="{{ $r['institution'] ?? '' }}" placeholder="University of Cebu" autocomplete="organization">
        </div>
        <div class="form-group">
            <label for="edu-deg-{{ $i }}">Degree</label>
            <input type="text" id="edu-deg-{{ $i }}" name="education[{{ $i }}][degree]" value="{{ $r['degree'] ?? '' }}" placeholder="Bachelor of Science">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="edu-field-{{ $i }}">Field of study</label>
            <input type="text" id="edu-field-{{ $i }}" name="education[{{ $i }}][field]" value="{{ $r['field'] ?? '' }}" placeholder="Information Management">
        </div>
        <div class="form-row" style="gap:12px;">
            <div class="form-group">
                <label for="edu-start-{{ $i }}">Start year</label>
                <input type="text" id="edu-start-{{ $i }}" name="education[{{ $i }}][start_year]" value="{{ $r['start_year'] ?? '' }}" placeholder="2022" inputmode="numeric" maxlength="4">
            </div>
            <div class="form-group">
                <label for="edu-end-{{ $i }}">End year</label>
                <input type="text" id="edu-end-{{ $i }}" name="education[{{ $i }}][end_year]" value="{{ $r['end_year'] ?? '' }}" placeholder="2026" maxlength="12">
            </div>
        </div>
    </div>
</div>
