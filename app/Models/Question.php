<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'image_path',
        'status',
    ];

    protected $casts = [
        'correct_answer' => 'string',
        'status' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getOptionsAttribute()
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];
    }

    public function getShuffledOptionsAndCorrectAnswer()
    {
        // Get all non-empty options
        $originalOptions = [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];

        // Filter out empty options
        $nonEmptyOptions = array_filter($originalOptions, function ($option) {
            return ! empty(trim($option));
        });

        // If we have less than 2 options, return original format
        if (count($nonEmptyOptions) < 2) {
            return [
                'options' => [
                    'option_a' => $this->option_a,
                    'option_b' => $this->option_b,
                    'option_c' => $this->option_c,
                    'option_d' => $this->option_d,
                ],
                'correct_answer' => $this->correct_answer,
            ];
        }

        // Get the correct answer text
        $correctAnswerText = $originalOptions[$this->correct_answer];

        // Create array with all non-empty options and their keys
        $optionsWithKeys = [];
        foreach ($nonEmptyOptions as $key => $value) {
            $optionsWithKeys[] = [
                'key' => $key,
                'value' => $value,
                'is_correct' => ($key === $this->correct_answer),
            ];
        }

        // Shuffle the options
        shuffle($optionsWithKeys);

        // Map to new positions
        $shuffledOptions = [
            'option_a' => '',
            'option_b' => '',
            'option_c' => '',
            'option_d' => '',
        ];

        $newCorrectAnswer = '';
        $optionKeys = ['a', 'b', 'c', 'd'];

        // Assign shuffled options to new positions
        for ($i = 0; $i < count($optionsWithKeys) && $i < 4; $i++) {
            $newOptionKey = 'option_'.$optionKeys[$i];
            $shuffledOptions[$newOptionKey] = $optionsWithKeys[$i]['value'];

            // Track where the correct answer ended up
            if ($optionsWithKeys[$i]['is_correct']) {
                $newCorrectAnswer = $optionKeys[$i];
            }
        }

        return [
            'options' => $shuffledOptions,
            'correct_answer' => $newCorrectAnswer,
        ];
    }

    public function getShuffledOptionsAttribute()
    {
        $shuffledData = $this->getShuffledOptionsAndCorrectAnswer();

        return $shuffledData['options'];
    }

    public function getShuffledCorrectAnswerAttribute()
    {
        $shuffledData = $this->getShuffledOptionsAndCorrectAnswer();

        return $shuffledData['correct_answer'];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
