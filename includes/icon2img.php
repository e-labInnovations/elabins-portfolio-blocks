<?php

function icon2img($iconName) {
  $icons = array(
    'ASP.NET' => 'https://user-images.githubusercontent.com/44733677/157992796-806ec850-b3a2-4ad1-8af6-7ce5cd4376e0.svg',
    'Assembly' => 'https://raw.githubusercontent.com/tandpfun/skill-icons/84be3e9ccacd6f6c44ff7f8c8078e89d9ca017ea/icons/asm.svg',
    'Astro' => 'https://skillicons.dev/icons?i=astro',
    'C' => 'https://skillicons.dev/icons?i=c',
    'C#' => 'https://skillicons.dev/icons?i=cs',
    'C++' => 'https://skillicons.dev/icons?i=cpp',
    'CMake' => 'https://skillicons.dev/icons?i=cmake',
    'CSS' => 'https://skillicons.dev/icons?i=css',
    'Clojure' => 'https://skillicons.dev/icons?i=clojure',
    'CoffeeScript' => 'https://skillicons.dev/icons?i=coffeescript',
    'Dart' => 'https://skillicons.dev/icons?i=dart',
    'Dockerfile' => 'https://skillicons.dev/icons?i=docker',
    'Gherkin' => 'https://skillicons.dev/icons?i=gherkin',
    'Go' => 'https://skillicons.dev/icons?i=go',
    'HTML' => 'https://skillicons.dev/icons?i=html',
    'Haskell' => 'https://skillicons.dev/icons?i=haskell',
    'Java' => 'https://skillicons.dev/icons?i=java',
    'JavaScript' => 'https://skillicons.dev/icons?i=js',
    'Kotlin' => 'https://skillicons.dev/icons?i=kotlin',
    'Less' => 'https://skillicons.dev/icons?i=less',
    'Lua' => 'https://skillicons.dev/icons?i=lua',
    'MATLAB' => 'https://skillicons.dev/icons?i=matlab',
    'Metal' => 'https://raw.githubusercontent.com/tandpfun/skill-icons/e4bccf6d701cd411a955688911f54a00d7690f1c/icons/Metal.svg',
    'Nix' => 'https://skillicons.dev/icons?i=nix',
    'OCaml' => 'https://skillicons.dev/icons?i=ocaml',
    'PHP' => 'https://skillicons.dev/icons?i=php',
    'Perl' => 'https://skillicons.dev/icons?i=perl',
    'PowerShell' => 'https://skillicons.dev/icons?i=powershell',
    'Processing' => 'https://skillicons.dev/icons?i=processing',
    'Pug' => 'https://skillicons.dev/icons?i=pug',
    'Python' => 'https://skillicons.dev/icons?i=python',
    'R' => 'https://skillicons.dev/icons?i=r',
    'Ruby' => 'https://skillicons.dev/icons?i=ruby',
    'Rust' => 'https://skillicons.dev/icons?i=rust',
    'SCSS' => 'https://skillicons.dev/icons?i=scss',
    'Shell' => 'https://skillicons.dev/icons?i=bash',
    'Svelte' => 'https://skillicons.dev/icons?i=svelte',
    'Swift' => 'https://skillicons.dev/icons?i=swift',
    'TypeScript' => 'https://skillicons.dev/icons?i=ts',
    'Vim Script' => 'https://skillicons.dev/icons?i=vim',
    'Vim Snippet' => 'https://skillicons.dev/icons?i=vim',
    'Vue' => 'https://skillicons.dev/icons?i=vue',
    'SVG' => 'https://skillicons.dev/icons?i=svg'
  );

  if (isset($icons[$iconName])) {
    return $icons[$iconName];
  }
  return ELABINS_PORTFOLIO_BLOCKS_URL . '/assets/default.svg';
}