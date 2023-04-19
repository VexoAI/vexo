<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Vexo\Weave\Chain\Concerns\SupportsValidation;
use Vexo\Weave\LLM\LLM;
use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\Prompt\Prompts;

final class LLMChain implements Chain
{
    use SupportsValidation;

    public function __construct(private LLM $llm)
    {
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);

        $response = $this->llm->generate(
            new Prompts(new Prompt($input->get('prompt')))
        );

        return new Output(
            ['text' => $response->generations()[0]->text()]
        );
    }

    private function inputConstraints(): Constraint
    {
        return new Assert\Collection([
            'prompt' => [
                new Assert\NotBlank()
            ]
        ]);
    }
}
