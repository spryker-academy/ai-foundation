# Spryker Academy — AI Foundation Exercises

Training repository (student skeleton branch) for the AI exercises of the Spryker Academy instructor-led training.
Load a branch with the exercise loader from the `instructor-exercises` repository:

```bash
./exercises/load.sh ai-foundation <branch>
```

| Exercise | Branch | What you build |
|----------|--------|----------------|
| 19: Hello AI | `advanced/ai-foundation-hello/skeleton` and `/complete` | A Storefront API endpoint (API Platform) that sends a message to an LLM through the AiFoundation client, with conversation memory |
| 20: Ask the Catalog | `advanced/ai-foundation-catalog/skeleton` and `/complete` | A Storefront API endpoint where the LLM reads a product through a tool in Zed and answers in a structured transfer |
| 21: Product Creation Agent | `advanced/ai-foundation-agent/skeleton` and `/complete` | A custom Back Office Assistant agent with tools, a tool set, and a dedicated AI configuration |

Guides live in the `instructor-exercises` repository under `guides/advanced/`.
Exercise 21 requires the Back Office Assistant. Exercises 19 and 20 only need AI Foundation and an OpenAI token.

Licensed under the MIT License.
