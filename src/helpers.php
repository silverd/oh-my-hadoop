<?php

if (! function_exists('sshToNCDH')) {
    function sshToNCDH(array $config, $commands)
    {
        $ssh = \Spatie\Ssh\Ssh::create($config['user'], $config['host'])
            ->disableStrictHostKeyChecking()
            ->usePrivateKey($config['prv_key']);

        $process = $ssh->execute($commands);

        $stderr = $process->getErrorOutput();
        $stdout = $process->getOutput();

        $result = json_decode($stdout, true) ?: [];

        if (! $process->isSuccessful()) {
            throw new \Exception('SSH Failed - ' . $stderr);
        }

        return $result;
    }
}
