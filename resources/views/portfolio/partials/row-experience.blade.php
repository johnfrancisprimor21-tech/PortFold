{{-- Variables: $i (row index), $r (array of saved/old values) --}}
<div class="repeatable-item" data-row>
    <button type="button" class="remove-btn" aria-label="Remove this experience entry" onclick="removeRow(this)">&times;</button>
    <div class="form-row">
        <div class="form-group">
            <label for="exp-company-{{ $i }}">Company</label>
            <input type="text" id="exp-company-{{ $i }}" name="experiences[{{ $i }}][company]" value="{{ $r['company'] ?? '' }}" placeholder="Company name" autocomplete="organization">
        </div>
        <div class="form-group">
            <label for="exp-role-{{ $i }}">Role / position</label>
            <input type="text" id="exp-role-{{ $i }}" name="experiences[{{ $i }}][role]" value="{{ $r['role'] ?? '' }}" placeholder="Web Developer">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="exp-start-{{ $i }}">Start date</label>
            <input type="text" id="exp-start-{{ $i }}" name="experiences[{{ $i }}][start_date]" value="{{ $r['start_date'] ?? '' }}" placeholder="Jun 2023">
        </div>
        <div class="form-group">
            <label for="exp-end-{{ $i }}">End date</label>
            <input type="text" id="exp-end-{{ $i }}" name="experiences[{{ $i }}][end_date]" value="{{ $r['end_date'] ?? '' }}" placeholder="Present">
            <span class="help">Leave empty if you still work here.</span>
        </div>
    </div>
    <div class="form-group">
        <label for="exp-desc-{{ $i }}">What did you do?</label>
        <textarea id="exp-desc-{{ $i }}" name="experiences[{{ $i }}][description]" placeholder="Describe your responsibilities and results...">{{ $r['description'] ?? '' }}</textarea>
    </div>
    <div class="form-group check">
        <input type="checkbox" id="exp-intern-{{ $i }}" name="experiences[{{ $i }}][is_internship]" value="1" {{ !empty($r['is_internship']) ? 'checked' : '' }}>
        <label for="exp-intern-{{ $i }}">This was an internship</label>
    </div>
</div>
