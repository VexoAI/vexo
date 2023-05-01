# Terminology

Vexo builds upon the following concepts.

* **Chain:** Chains are generic way to combine individual components to enable more complex workflows or use cases that go beyond a single interaction with a language model. Sequential chains are the most common which are generally used to have the output from one model or tool feed into the next one while maintaining overall context.
* **Language Model:** Language Models are computational frameworks or systems that are designed to process, understand, and generate human-like text based on input data. Language Models are generally stateless and do not remember past interactions.
* **Prompt:** The input text or sequence of words provided to a model, which services as a starting point or cue for the model to generate a response. The prompt is used to guide the model's behavior and helps it understand the context and the user's intention.
* **Agent:** While a language model simply generates a response based on a prompt, an agent leverages those models as reasoning engines to perform more complicated tasks. Agents generally have the ability to remember and construct context which is fed into the models, and have the ability to use external tools and data. Sometimes they can also act autonomously over longer periods of time.
* **Index:** A way to structure large amounts of information or documents so models can interact with them in an efficient and consistent manner.
* **Memory:** A way for agents to remember and retrieve information over multiple interactions. Memory can be used as short-term memory to maintain context during more involved workflows, but can also be used as long-term memory to track information over multiple independent interactions.
