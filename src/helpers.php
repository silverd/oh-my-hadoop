<?php

if (! function_exists('sshToNCDH')) {
    function sshToNCDH(array $config, $commands)
    {
        $ssh = \Spatie\Ssh\Ssh::create($config['user'], $config['host'], $config['port'] ?? null)
            ->disableStrictHostKeyChecking()
            ->usePrivateKey($config['prv_key']);

        $process = $ssh->execute($commands);

        $stderr = $process->getErrorOutput();
        $stdout = $process->getOutput();

        if (! $process->isSuccessful()) {
            throw new \Exception('SSH Failed - ' . $stderr);
        }

        return $stdout;
    }
}

if (! function_exists('sshToNCDHJson')) {
    function sshToNCDHJson(array $config, $commands)
    {
        $stdout = sshToNCDH($config, $commands);

        $result = json_decode($stdout, true) ?: [];

        if (! isset($result['code']) || $result['code'] != 0) {
            throw new \Exception('SSH Error - ' . $stdout);
        }

        return $result;
    }
}
