#!/usr/bin/env python3
"""
Codex CLI - Main command line interface.
A powerful CLI for interacting with multiple LLM providers.
"""

import sys
import argparse
import json
from typing import Optional, List

from .config import Config
from .providers import get_provider, list_providers


def cmd_init(args):
    """Initialize configuration."""
    config = Config()
    config.save()
    print(f"✓ Configuration initialized at {config.config_path}")
    print("\nNext steps:")
    print("  1. Set your API keys: codex config set --provider <name> --key <key>")
    print("  2. Or set environment variables (e.g., CODEX_GROQ_API_KEY)")
    print("  3. Start chatting: codex chat")


def cmd_config_set(args):
    """Set configuration value."""
    config = Config()
    
    if args.key:
        config.set_api_key(args.provider, args.key)
        print(f"✓ API key set for provider: {args.provider}")
    
    if args.model:
        if "providers" not in config.config:
            config.config["providers"] = {}
        if args.provider not in config.config["providers"]:
            config.config["providers"][args.provider] = {}
        config.config["providers"][args.provider]["default_model"] = args.model
        config.save()
        print(f"✓ Default model set for {args.provider}: {args.model}")


def cmd_config_set_default(args):
    """Set default provider."""
    config = Config()
    config.set_default_provider(args.provider)
    print(f"✓ Default provider set to: {args.provider}")


def cmd_config_list(args):
    """List configuration."""
    config = Config()
    
    print("\n=== Codex CLI Configuration ===\n")
    print(f"Config file: {config.config_path}")
    print(f"Default provider: {config.get_default_provider()}")
    
    print("\n--- Configured Providers ---")
    configured = config.list_providers()
    if configured:
        for provider in configured:
            is_configured = "✓" if config.is_provider_configured(provider) else "✗"
            model = config.get_default_model(provider)
            print(f"  {is_configured} {provider}: {model}")
    else:
        print("  No providers configured yet.")
    
    print("\n--- Settings ---")
    settings = config.config.get("settings", {})
    for key, value in settings.items():
        print(f"  {key}: {value}")
    
    print("\n--- Available Providers ---")
    for provider in config.PROVIDERS:
        status = "configured" if provider in configured else "not configured"
        print(f"  • {provider} ({status})")


def cmd_ask(args):
    """Ask a question."""
    config = Config()
    provider_name = args.provider or config.get_default_provider()
    api_key = config.get_api_key(provider_name)
    
    if not api_key:
        print(f"Error: No API key found for provider '{provider_name}'")
        print("Set it with: codex config set --provider {} --key <key>".format(provider_name))
        print("Or set environment variable: CODEX_{}_API_KEY".format(provider_name.upper()))
        sys.exit(1)
    
    model = args.model or config.get_default_model(provider_name)
    
    try:
        provider = get_provider(provider_name, api_key, {"model": model})
        
        kwargs = {
            "temperature": args.temperature or config.get_setting("temperature", 0.7),
            "max_tokens": args.max_tokens or config.get_setting("max_tokens", 2048),
        }
        
        if args.stream:
            print(f"\n[{provider_name}/{model}]\n")
            for chunk in provider.generate_stream(args.prompt, **kwargs):
                print(chunk, end="", flush=True)
            print("\n")
        else:
            response = provider.generate(args.prompt, **kwargs)
            print(f"\n{response}\n")
            
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)


def cmd_chat(args):
    """Interactive chat mode."""
    config = Config()
    provider_name = args.provider or config.get_default_provider()
    api_key = config.get_api_key(provider_name)
    
    if not api_key:
        print(f"Error: No API key found for provider '{provider_name}'")
        print("Set it with: codex config set --provider {} --key <key>".format(provider_name))
        sys.exit(1)
    
    model = args.model or config.get_default_model(provider_name)
    
    try:
        provider = get_provider(provider_name, api_key, {"model": model})
        
        kwargs = {
            "temperature": config.get_setting("temperature", 0.7),
            "max_tokens": config.get_setting("max_tokens", 2048),
        }
        
        print(f"\n=== Chat with {provider_name} ({model}) ===")
        print("Type 'quit' or 'exit' to end the conversation.\n")
        
        messages = []
        
        if args.system:
            messages.append({"role": "system", "content": args.system})
        
        while True:
            try:
                user_input = input(">>> ").strip()
            except (EOFError, KeyboardInterrupt):
                print("\nGoodbye!")
                break
            
            if user_input.lower() in ["quit", "exit", "q"]:
                print("Goodbye!")
                break
            
            if not user_input:
                continue
            
            messages.append({"role": "user", "content": user_input})
            
            try:
                # Simple implementation - just send the last message
                response = provider.generate(user_input, **kwargs)
                print(f"\n{response}\n")
                messages.append({"role": "assistant", "content": response})
            except Exception as e:
                print(f"Error: {e}")
                
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)


def cmd_code(args):
    """Generate code."""
    config = Config()
    provider_name = args.provider or config.get_default_provider()
    api_key = config.get_api_key(provider_name)
    
    if not api_key:
        print(f"Error: No API key found for provider '{provider_name}'")
        sys.exit(1)
    
    model = args.model or config.get_default_model(provider_name)
    
    system_prompt = "You are an expert programmer. Generate clean, well-commented code."
    if args.lang:
        system_prompt += f" Write the code in {args.lang}."
    
    prompt = args.description
    if args.lang:
        prompt += f"\n\nLanguage: {args.lang}"
    
    try:
        provider = get_provider(provider_name, api_key, {"model": model})
        
        kwargs = {
            "temperature": 0.2,  # Lower temperature for code
            "max_tokens": args.max_tokens or config.get_setting("max_tokens", 4096),
        }
        
        # Add system prompt context
        full_prompt = f"{system_prompt}\n\nTask: {prompt}"
        
        if args.stream:
            for chunk in provider.generate_stream(full_prompt, **kwargs):
                print(chunk, end="", flush=True)
            print()
        else:
            response = provider.generate(full_prompt, **kwargs)
            
            if args.output:
                with open(args.output, "w") as f:
                    f.write(response)
                print(f"✓ Code written to {args.output}")
            else:
                print(response)
                
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)


def cmd_models(args):
    """List available models."""
    config = Config()
    
    print("\n=== Available Models by Provider ===\n")
    
    if args.provider:
        providers_to_show = [args.provider]
    else:
        providers_to_show = config.PROVIDERS
    
    for provider_name in providers_to_show:
        default_model = config.get_default_model(provider_name)
        print(f"{provider_name}:")
        print(f"  Default: {default_model}")
        print()


def cmd_compare(args):
    """Compare responses across providers."""
    config = Config()
    
    providers = args.providers.split(",") if args.providers else [config.get_default_provider()]
    
    print(f"\n=== Comparing Providers ===")
    print(f"Prompt: {args.prompt}\n")
    
    for provider_name in providers:
        api_key = config.get_api_key(provider_name)
        if not api_key:
            print(f"[{provider_name}] ✗ Not configured")
            continue
        
        try:
            model = config.get_default_model(provider_name)
            provider = get_provider(provider_name, api_key, {"model": model})
            
            print(f"[{provider_name}/{model}]")
            response = provider.generate(args.prompt, temperature=0.7, max_tokens=1024)
            print(f"{response}\n")
            
        except Exception as e:
            print(f"[{provider_name}] Error: {e}\n")


def main():
    parser = argparse.ArgumentParser(
        prog="codex",
        description="Codex CLI - Multi-LLM Command Line Tool",
    )
    parser.add_argument("--version", action="version", version="codex 0.1.0")
    
    subparsers = parser.add_subparsers(dest="command", help="Commands")
    
    # init command
    init_parser = subparsers.add_parser("init", help="Initialize configuration")
    init_parser.set_defaults(func=cmd_init)
    
    # config command
    config_parser = subparsers.add_parser("config", help="Manage configuration")
    config_subparsers = config_parser.add_subparsers(dest="config_command")
    
    # config set
    config_set_parser = config_subparsers.add_parser("set", help="Set configuration value")
    config_set_parser.add_argument("--provider", required=True, help="Provider name")
    config_set_parser.add_argument("--key", help="API key")
    config_set_parser.add_argument("--model", help="Default model")
    config_set_parser.set_defaults(func=cmd_config_set)
    
    # config set-default
    config_default_parser = config_subparsers.add_parser("set-default", help="Set default provider")
    config_default_parser.add_argument("--provider", required=True, help="Provider name")
    config_default_parser.set_defaults(func=cmd_config_set_default)
    
    # config list
    config_list_parser = config_subparsers.add_parser("list", help="List configuration")
    config_list_parser.set_defaults(func=cmd_config_list)
    
    # ask command
    ask_parser = subparsers.add_parser("ask", help="Ask a question")
    ask_parser.add_argument("prompt", help="Your question or prompt")
    ask_parser.add_argument("--provider", "-p", help="Provider to use")
    ask_parser.add_argument("--model", "-m", help="Model to use")
    ask_parser.add_argument("--temperature", "-t", type=float, help="Temperature")
    ask_parser.add_argument("--max-tokens", type=int, help="Max tokens")
    ask_parser.add_argument("--stream", "-s", action="store_true", help="Stream response")
    ask_parser.set_defaults(func=cmd_ask)
    
    # chat command
    chat_parser = subparsers.add_parser("chat", help="Interactive chat mode")
    chat_parser.add_argument("--provider", "-p", help="Provider to use")
    chat_parser.add_argument("--model", "-m", help="Model to use")
    chat_parser.add_argument("--system", help="System prompt")
    chat_parser.set_defaults(func=cmd_chat)
    
    # code command
    code_parser = subparsers.add_parser("code", help="Generate code")
    code_parser.add_argument("description", help="Description of code to generate")
    code_parser.add_argument("--provider", "-p", help="Provider to use")
    code_parser.add_argument("--model", "-m", help="Model to use")
    code_parser.add_argument("--lang", "-l", help="Programming language")
    code_parser.add_argument("--output", "-o", help="Output file")
    code_parser.add_argument("--max-tokens", type=int, help="Max tokens")
    code_parser.add_argument("--stream", "-s", action="store_true", help="Stream response")
    code_parser.set_defaults(func=cmd_code)
    
    # models command
    models_parser = subparsers.add_parser("models", help="List available models")
    models_parser.add_argument("--provider", "-p", help="Filter by provider")
    models_parser.set_defaults(func=cmd_models)
    
    # compare command
    compare_parser = subparsers.add_parser("compare", help="Compare providers")
    compare_parser.add_argument("prompt", help="Prompt to compare")
    compare_parser.add_argument("--providers", help="Comma-separated list of providers")
    compare_parser.set_defaults(func=cmd_compare)
    
    args = parser.parse_args()
    
    if not args.command:
        parser.print_help()
        sys.exit(0)
    
    if hasattr(args, "func"):
        args.func(args)
    else:
        parser.print_help()


if __name__ == "__main__":
    main()
