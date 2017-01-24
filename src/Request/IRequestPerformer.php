<?php


namespace StardogPhp\Request;


interface IRequestPerformer
{

    /**
     * @param Request $request
     * @return Response
     */
    public function performRequest(Request $request);

}