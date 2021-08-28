<?php

namespace dcms\enqueu\helpers;

abstract class State{
   const pending = 0;
   const sent = 1;
   const error = -1;
}

abstract class StateName{
   const pending = 'pending';
   const sent = 'sent';
   const error = 'error';
}