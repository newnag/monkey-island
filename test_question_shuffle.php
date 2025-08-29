<?php

// Simple test script to verify question shuffling logic
require_once 'vendor/autoload.php';

// Mock Question class for testing
class TestQuestion
{
    public $option_a;

    public $option_b;

    public $option_c;

    public $option_d;

    public $correct_answer;

    public function __construct($option_a, $option_b, $option_c, $option_d, $correct_answer)
    {
        $this->option_a = $option_a;
        $this->option_b = $option_b;
        $this->option_c = $option_c;
        $this->option_d = $option_d;
        $this->correct_answer = $correct_answer;
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
}

// Test with sample question
echo "Testing Question Shuffling Logic\n";
echo "=====================================\n";

$question = new TestQuestion(
    'ตัวเลือก A',
    'ตัวเลือก B',
    'ตัวเลือก C',
    'ตัวเลือก D',
    'b'  // คำตอบที่ถูกคือ B
);

echo "Original Question:\n";
echo 'A: '.$question->option_a."\n";
echo 'B: '.$question->option_b." [CORRECT]\n";
echo 'C: '.$question->option_c."\n";
echo 'D: '.$question->option_d."\n";
echo 'Original correct answer: '.$question->correct_answer."\n\n";

// Test shuffling multiple times
for ($i = 1; $i <= 5; $i++) {
    echo "Test #$i - Shuffled:\n";
    $shuffled = $question->getShuffledOptionsAndCorrectAnswer();

    echo 'A: '.$shuffled['options']['option_a'].($shuffled['correct_answer'] === 'a' ? ' [CORRECT]' : '')."\n";
    echo 'B: '.$shuffled['options']['option_b'].($shuffled['correct_answer'] === 'b' ? ' [CORRECT]' : '')."\n";
    echo 'C: '.$shuffled['options']['option_c'].($shuffled['correct_answer'] === 'c' ? ' [CORRECT]' : '')."\n";
    echo 'D: '.$shuffled['options']['option_d'].($shuffled['correct_answer'] === 'd' ? ' [CORRECT]' : '')."\n";
    echo 'New correct answer: '.$shuffled['correct_answer']."\n";

    // Verify the correct answer text matches
    $correctText = 'ตัวเลือก B';  // Original correct answer text
    $newCorrectText = $shuffled['options']['option_'.$shuffled['correct_answer']];
    echo 'Verification: '.($correctText === $newCorrectText ? '✓ PASS' : '✗ FAIL')."\n";
    echo "Expected: '$correctText', Got: '$newCorrectText'\n";
    echo "-----------\n";
}
